<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Хто може редагувати?
     */
    public function update(User $user, Post $post): bool
    {
        // 1. Superadmin може все
        if ($user->role === 'superadmin') {
            return true;
        }

        // 2. Admin може редагувати ТІЛЬКИ якщо він призначений менеджером цього поста
        if ($user->role === 'admin' && $post->manager_id === $user->id) {
            return true;
        }

        // 3. Автор поста може його редагувати (якщо ти хочеш залишити це право юзеру)
        return $user->id === $post->user_id;
    }

    /**
     * Хто може видалити?
     */
    public function delete(User $user, Post $post): bool
    {
        // Аналогічно до редагування
        if ($user->role === 'superadmin') return true;

        if ($user->role === 'admin' && $post->manager_id === $user->id) return true;

        return $user->id === $post->user_id;
    }
}
