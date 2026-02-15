<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    // Дозволяємо масове заповнення полів (важливо для створення постів)
    protected $fillable = ['title', 'content', 'user_id', 'source', 'external_id'];

    /**
     * Описуємо зв'язок: Пост НАЛЕЖИТЬ користувачу
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
