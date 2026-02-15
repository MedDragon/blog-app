<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::all()->random()->id, // Беремо випадкового існуючого юзера
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'source' => fake()->randomElement(['Source 1', 'Source 2', 'Source 3', 'Source 4', 'Source 5']),
            'external_id' => fake()->uuid(),
        ];
    }
}
