<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function store(Request $request)
    {
        // Валідація даних
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string',
        ]);

        // Створення поста
        Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'source' => $validated['source'],
            'user_id' => Auth::id(), // ID поточного юзера
            'external_id' => (string) str()->uuid(), // Імітуємо генерацію ID для Zoho
        ]);

        return redirect()->back(); // Повертаємо користувача назад
    }
}
