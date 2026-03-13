<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'slug' => str($title)->slug()->toString(),
            'title' => str($title)->title()->toString(),
            'short_description' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['math', 'logic', 'memory']),
            'difficulty' => fake()->randomElement(['easy', 'medium']),
            'thumbnail' => null,
            'route_path' => null,
            'play_count' => fake()->numberBetween(0, 1000),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }
}
