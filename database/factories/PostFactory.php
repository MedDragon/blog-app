<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            // Використовуємо функцію для перевірки наявності юзерів, щоб не було помилки при чистій базі
            'user_id' => User::exists() ? User::inRandomOrder()->first()->id : User::factory(),
            'title' => fake()->realText(50), // realText генерує більш змістовні фрази мовою локалізації
            'content' => fake()->realText(500), // Текст посту українською
            'source' => 'Джерело ' . fake()->numberBetween(1, 5), // Локалізуємо і джерела
            'external_id' => fake()->uuid(),
        ];
    }
}
