<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Services\DealDistributionService;
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
        // 1. Супер-адмін
        User::factory()->create([
            'name' => 'Artem Boss',
            'email' => 'admin@example.com',
            'role' => 'superadmin',
            'password' => bcrypt('1'),
        ]);

        // 2. Створюємо адмінів з різною вагою
        $admin1 = User::factory()->create(['name' => 'Pro Admin', 'role' => 'admin', 'assignment_weight' => 70, 'email' => 'admin1@example.com', 'password' => bcrypt('1')]);
        $admin2 = User::factory()->create(['name' => 'Junior Admin', 'role' => 'admin', 'assignment_weight' => 30, 'email' => 'admin2@example.com', 'password' => bcrypt('1')]);

        // 3. Юзери
        $users = User::factory(10)->create(['role' => 'user']);

        // 4. Створюємо пости і розподіляємо їх
        $service = new DealDistributionService;

        Post::factory(20)->create()->each(function ($post) use ($service) {
            $service->assignManager($post);
        });
    }
}
