<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DealDistributionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'new');

        $posts = Post::with([
            'user',
            'manager',
            'comments' => function($query) {
                $query->with(['user', 'likes'])->withCount('likes');
            },
            'likes'
        ])
            ->withCount(['likes', 'comments'])
            // ЛОГІКА ВІДОБРАЖЕННЯ:
            ->when($user->role === 'admin', function ($query) use ($user) {
                // Менеджер бачить ТІЛЬКИ свої призначені пости
                return $query->where('manager_id', $user->id);
            })
            // (Опціонально) Якщо хочеш, щоб юзери бачили тільки свої, додай аналогічний when для 'user'

            // Сортування
            ->when($filter === 'new', fn($q) => $q->latest())
            ->when($filter === 'old', fn($q) => $q->oldest())
            ->when($filter === 'popular', fn($q) => $q->orderBy('likes_count', 'desc'))
            ->when($filter === 'discussed', fn($q) => $q->orderBy('comments_count', 'desc'))
            ->get()
            ->map(function ($post) {
                // Лайк поста
                $post->is_liked = Auth::check() && $post->likes->where('user_id', Auth::id())->isNotEmpty();

                // Лайки коментарів
                $post->comments->each(function ($comment) {
                    $comment->is_liked = Auth::check() && $comment->likes->where('user_id', Auth::id())->isNotEmpty();
                });

                // Сортування коментарів за популярністю
                $post->setRelation('comments', $post->comments->sortByDesc('likes_count')->values());

                return $post;
            });

        return Inertia::render('Dashboard', [
            'posts' => $posts,
            'currentFilter' => $filter
        ]);
    }

    public function store(Request $request, DealDistributionService $distributor)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'external_id' => (string) str()->uuid(),
        ]);

        // Призначаємо адміна згідно з відсотками (вагою)
        $distributor->assignManager($post);

        return redirect()->back()->with('success', 'Пост створено та призначено менеджеру!');
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return back()->with('status', 'Пост успішно оновлено!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return back()->with('status', 'Пост видалено');
    }
}
