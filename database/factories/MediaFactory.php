<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['anime', 'drama']);

        return [
            'title' => fake()->unique()->words(3, true),
            'type' => $type,
            'genre' => fake()->randomElement(['Action', 'Fantasy', 'Romance', 'Sports', 'Mystery']),
            'year' => fake()->numberBetween(2010, 2026),
            'episodes' => fake()->numberBetween(12, 36),
            'country' => $type === 'drama' ? fake()->randomElement(['Korea Selatan', 'Jepang', 'Thailand']) : null,
        ];
    }
}
