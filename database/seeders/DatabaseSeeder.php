<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
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
        // Створюємо тестового пешого
        User::factory()->create([
            'name' => 'Artem Test',
            'email' => 'test@example.com',
            'password' => bcrypt('1'), // щоб точно знати пароль
        ]);

        // Потім створюємо інших
        User::factory(10)->create();
        Post::factory(30)->create();
    }
}
