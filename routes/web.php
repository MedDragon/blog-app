<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ВИПРАВЛЕНИЙ DASHBOARD
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'posts' => Post::with([
            'user',
            // Завантажуємо коментарі з їхніми авторами, відповідями та ЛАЙКАМИ до коментарів
            'comments' => function($query) {
                $query->with(['user', 'replies.user', 'likes'])->withCount('likes');
            },
            'likes' // Завантажуємо лайки до постів
        ])
            ->withCount(['likes', 'comments']) // Рахуємо лайки та коменти для ПОСТА
            ->latest()
            ->get()
            ->map(function ($post) {
                // Додаємо поле is_liked для фронтенду
                $post->is_liked = $post->likes->where('user_id', auth()->id())->isNotEmpty();

                $post->comments->map(function ($comment) {
                    $comment->is_liked = $comment->likes->where('user_id', auth()->id())->isNotEmpty();
                    return $comment;
                });

                return $post;
            })
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Пости
Route::post('/posts', [PostController::class, 'store'])
    ->middleware(['auth'])
    ->name('posts.store');

// Коментарі
Route::post('/comments', [CommentController::class, 'store'])
    ->middleware(['auth'])
    ->name('comments.store');

// Лайки
Route::post('/likes/toggle', [LikeController::class, 'toggle'])
    ->middleware(['auth'])
    ->name('likes.toggle');

// Оновлення постів
Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');

// Оновлення коментарів
Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');

Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

require __DIR__.'/auth.php';
