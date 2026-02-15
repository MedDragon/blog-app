<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $id = $request->id;
        $type = $request->type === 'post' ? Post::class : Comment::class;
        $userId = auth()->id();

        $like = Like::where('user_id', $userId)
            ->where('likeable_id', $id)
            ->where('likeable_type', $type)
            ->first();

        if ($like) {
            $like->delete(); // Прибираємо лайк
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_id' => $id,
                'likeable_type' => $type,
            ]);
        }

        return back();
    }
}
