<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// Стрічка тепер доступна ВСІМ (і гостям, і юзерам)
Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');

// Тільки для авторизованих
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/control', [AdminController::class, 'index'])->name('control');
        Route::patch('/users/{user}/weight', [AdminController::class, 'updateWeight'])->name('update-weight');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('update-role');
        Route::post('/login-as/{user}', [AdminController::class, 'loginAs'])->name('login-as');
    });
});

require __DIR__.'/auth.php';
