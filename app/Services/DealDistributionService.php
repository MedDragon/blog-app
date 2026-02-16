<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;

class DealDistributionService {
    public function assignManager(Post $post) {
        $admins = User::where('role', 'admin')
            ->where('assignment_weight', '>', 0)
            ->get();

        if ($admins->isEmpty()) return;

        $chosenManager = $admins->sortBy(function ($admin) {
            // Використовуємо запит до БД щоразу, щоб бачити реальну кількість
            // Це важливо саме під час сидіння бази
            $currentCount = Post::where('manager_id', $admin->id)->count();

            return $currentCount / $admin->assignment_weight;
        })->first();

        if ($chosenManager) {
            // Використовуємо forceFill або quiet оновлення, якщо не хочеш зайвих подій,
            // але звичайний update теж ок
            $post->manager_id = $chosenManager->id;
            $post->save();
        }
    }
}
