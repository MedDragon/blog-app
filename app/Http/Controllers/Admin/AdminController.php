<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        return Inertia::render('Admin/Control', [ // Нова назва в'юшки
            'admins' => User::where('role', 'admin')
                ->withCount('managedPosts')
                ->get(),
            'allUsers' => User::where('role', 'user')->get(['id', 'name', 'email', 'assignment_weight'])
        ]);
    }

    public function updateWeight(Request $request, User $user)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|min:0'
        ]);

        // Пряме оновлення через об'єкт
        $user->assignment_weight = (int) $validated['weight'];
        $user->save();

        return back();
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string'
        ]);

        $user->role = $validated['role'];

        // Якщо стає адміном - даємо 10% для старту
        if ($validated['role'] === 'admin') {
            $user->assignment_weight = 10;
        } else {
            $user->assignment_weight = 0;
        }

        $user->save();

        return back();
    }

    public function loginAs(User $user)
    {
        // Тільки суперадмін може "красти" сесії
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Ви увійшли як ' . $user->name);
    }
}
