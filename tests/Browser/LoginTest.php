<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\Browser\Pages\Login;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_user_can_login_using_page_object(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                // Замість loginUser зробимо кроки явно для надійності
                ->type('@email', 'admin@example.com')
                ->type('@password', '1')
                ->press('УВІЙТИ')
                ->pause(1000) // Дамо Inertia.js секунду на рендер
                ->assertPathIsNot('/login'); // Якщо ми вже не на login - значить пройшли далі!
        });
    }
}
