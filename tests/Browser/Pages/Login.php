<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Login extends Page
{
    public function url(): string
    {
        return '/login'; // URL сторінки логіну
    }

    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    public function elements(): array
    {
        return [
            '@email' => '#email',
            '@password' => '#password',
            // Використовуємо стандартний селектор кнопки за типом
            '@submit' => 'button[type="submit"]',
        ];
    }

    /**
     * Спрощений метод для авторизації
     */
    public function loginUser(Browser $browser, $email, $password)
    {
        $browser->type('@email', $email)
            ->type('@password', $password)
            ->click('@submit');
    }
}
