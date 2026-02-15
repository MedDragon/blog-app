<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
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
        ]);

        return redirect()->back();
    }
}
