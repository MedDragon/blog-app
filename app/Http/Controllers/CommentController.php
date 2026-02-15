<?php

namespace App\Http\Controllers;

use App\Models\Comment; // Тепер підключено правильно
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
            'post_id' => 'required|exists:posts,id',
        ]);

        Comment::create([
            'body' => $validated['body'],
            'post_id' => $validated['post_id'],
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Comment $comment) // Короткий запис
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment->update($validated);

        return back()->with('status', 'Коментар оновлено!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back();
    }
}
