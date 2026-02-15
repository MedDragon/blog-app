<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'post_id', 'user_id', 'parent_id'];

    // Коментар належить користувачу
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Коментар належить посту
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes() {
        return $this->morphMany(Like::class, 'likeable');
    }

// Допоміжне поле, щоб знати, чи лайкнув поточний юзер цей об'єкт
    public function getIsLikedAttribute() {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }
}
