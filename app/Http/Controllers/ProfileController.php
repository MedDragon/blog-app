<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Заповнюємо базові дані, ігноруючи аватар (його обробимо окремо)
        $user->fill($request->safe()->except(['avatar', 'avatar_url']));

        // 2. ЛОГІКА АВАТАРА
        if ($request->hasFile('avatar')) {
            // Пріоритет 1: Якщо завантажено новий файл
            $file = $request->file('avatar');
            $fileName = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $fileName, 'public');
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar_url')) {
            // Пріоритет 2: Якщо файлу немає, але вставили посилання
            $user->avatar = $request->input('avatar_url');
        }
        // Пріоритет 3: Якщо обидва поля порожні, ми не чіпаємо старий $user->avatar

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
