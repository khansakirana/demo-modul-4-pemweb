<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_filter_media_by_title_and_type(): void
    {
        Media::create([
            'title' => 'Frieren: Beyond Journey End',
            'type' => 'anime',
            'genre' => 'Fantasy',
            'year' => 2023,
            'episodes' => 28,
        ]);

        Media::create([
            'title' => 'Queen of Tears',
            'type' => 'drama',
            'genre' => 'Romance',
            'year' => 2024,
            'episodes' => 16,
            'country' => 'Korea Selatan',
        ]);

        $response = $this->getJson('/api/media');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'genre', 'year', 'episodes', 'country', 'details', 'users'],
                ],
            ]);

        $filtered = $this->getJson('/api/media?title=Frieren');
        $filtered->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Frieren: Beyond Journey End')
            ->assertJsonPath('data.0.details', '[Anime] Frieren: Beyond Journey End | Genre: Fantasy | Tahun: 2023 | Episode: 28');

        $byType = $this->getJson('/api/media?type=drama');
        $byType->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Queen of Tears');
    }

    public function test_can_create_anime_and_drama_and_reject_invalid_payloads(): void
    {
        $animeResponse = $this->postJson('/api/media', [
            'title' => 'Solo Leveling',
            'type' => 'anime',
            'genre' => 'Action',
            'year' => 2024,
            'episodes' => 12,
        ]);

        $animeResponse->assertStatus(201)
            ->assertJsonPath('data.title', 'Solo Leveling')
            ->assertJsonPath('data.country', null);

        // Duplicate title should fail with 422
        $duplicateResponse = $this->postJson('/api/media', [
            'title' => 'Solo Leveling',
            'type' => 'anime',
            'genre' => 'Action',
            'year' => 2024,
            'episodes' => 12,
        ]);
        $duplicateResponse->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Empty title should fail with 422
        $emptyTitleResponse = $this->postJson('/api/media', [
            'title' => '',
            'type' => 'anime',
            'genre' => 'Action',
            'year' => 2024,
            'episodes' => 12,
        ]);
        $emptyTitleResponse->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Drama without country should fail with 422
        $dramaMissingCountry = $this->postJson('/api/media', [
            'title' => 'Moving',
            'type' => 'drama',
            'genre' => 'Action',
            'year' => 2023,
            'episodes' => 20,
        ]);
        $dramaMissingCountry->assertStatus(422)
            ->assertJsonValidationErrors(['country']);

        // Valid drama creation
        $dramaResponse = $this->postJson('/api/media', [
            'title' => 'Moving',
            'type' => 'drama',
            'genre' => 'Action',
            'year' => 2023,
            'episodes' => 20,
            'country' => 'Korea Selatan',
        ]);
        $dramaResponse->assertStatus(201)
            ->assertJsonPath('data.country', 'Korea Selatan');
    }

    public function test_can_show_update_and_delete_media(): void
    {
        $media = Media::create([
            'title' => 'Haikyuu!!',
            'type' => 'anime',
            'genre' => 'Sports',
            'year' => 2014,
            'episodes' => 25,
        ]);

        $this->getJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Haikyuu!!')
            ->assertJsonStructure(['data' => ['id', 'title', 'users']]);

        // Partial PATCH update (keeping same title shouldn't trigger unique conflict)
        $this->patchJson("/api/media/{$media->id}", [
            'episodes' => 85,
        ])->assertStatus(200)
            ->assertJsonPath('data.episodes', 85)
            ->assertJsonPath('data.title', 'Haikyuu!!');

        // Not found returns 404
        $this->getJson('/api/media/9999')
            ->assertStatus(404);

        $this->deleteJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus']);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_user_collection_attach_duplicate_show_and_detach_flow(): void
    {
        $user = User::factory()->create(['name' => 'khansa']);
        $media = Media::create([
            'title' => 'Frieren: Beyond Journey End',
            'type' => 'anime',
            'genre' => 'Fantasy',
            'year' => 2023,
            'episodes' => 28,
        ]);

        // Happy path: attach media to user collection
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
        ])->assertStatus(200)
            ->assertJsonPath('message', 'Media berhasil ditambahkan ke koleksi');

        // Failure path: duplicate attach returns 409 Conflict
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
        ])->assertStatus(409)
            ->assertJson(['message' => 'Media sudah ada di koleksi']);

        // Failure path: nonexistent media_id returns 422
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => 9999,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);

        // Show user with collection (whenLoaded)
        $this->getJson("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'khansa')
            ->assertJsonCount(1, 'data.media');

        // Dedicated collection endpoint
        $this->getJson("/api/users/{$user->id}/collection")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Show media with users (whenLoaded)
        $this->getJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.users')
            ->assertJsonPath('data.users.0.name', 'khansa');

        // Happy path: detach media from collection
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus dari koleksi']);

        // Failure path: detaching media not in collection returns 404
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(404);
    }

    public function test_user_crud_endpoints(): void
    {
        $create = $this->postJson('/api/users', [
            'name' => 'Budi',
            'email' => 'budi@flipplay.test',
            'password' => 'secret123',
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('data.name', 'Budi');

        $userId = $create->json('data.id');

        $this->patchJson("/api/users/{$userId}", [
            'name' => 'Budi Santoso',
        ])->assertStatus(200)
            ->assertJsonPath('data.name', 'Budi Santoso');

        $this->getJson('/api/users')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->deleteJson("/api/users/{$userId}")
            ->assertStatus(200);
    }
}
