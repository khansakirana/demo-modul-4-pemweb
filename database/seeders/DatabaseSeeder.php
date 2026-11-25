<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dhea = User::create([
            'name' => 'Dhea',
            'email' => 'dhea@flipplay.test',
            'password' => 'password',
        ]);

        User::create([
            'name' => 'Raka',
            'email' => 'raka@flipplay.test',
            'password' => 'password',
        ]);

        $frieren = Media::create([
            'title' => 'Frieren: Beyond Journey End',
            'type' => 'anime',
            'genre' => 'Fantasy',
            'year' => 2023,
            'episodes' => 28,
            'country' => null,
        ]);

        Media::create([
            'title' => 'Haikyuu!!',
            'type' => 'anime',
            'genre' => 'Sports',
            'year' => 2014,
            'episodes' => 25,
            'country' => null,
        ]);

        $queenOfTears = Media::create([
            'title' => 'Queen of Tears',
            'type' => 'drama',
            'genre' => 'Romance',
            'year' => 2024,
            'episodes' => 16,
            'country' => 'Korea Selatan',
        ]);

        Media::create([
            'title' => 'Moving',
            'type' => 'drama',
            'genre' => 'Action',
            'year' => 2023,
            'episodes' => 20,
            'country' => 'Korea Selatan',
        ]);

        // Contoh koleksi awal milik Dhea
        $dhea->media()->attach([$frieren->id, $queenOfTears->id]);
    }
}
