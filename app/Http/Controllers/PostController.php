<?php

namespace App\Http\Controllers;

use App\Models\Post; // Тепер підключено правильно
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string',
        ]);

        Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'source' => $validated['source'],
            'user_id' => Auth::id(),
            'external_id' => (string) str()->uuid(),
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Post $post) // Короткий запис
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return back()->with('status', 'Пост успішно оновлено!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Ви не можете видалити чужий пост.');
        }

        $post->delete();

        return back()->with('status', 'Пост видалено');
    }
}
