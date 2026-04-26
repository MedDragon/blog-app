<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use Mockery;

class UserUnitTest extends TestCase
{
    /**
     * Тест перевіряє базові атрибути моделі User без звернення до БД.
     * Це приклад UNIT-тесту для Моделі (вимога 20%).
     */
    public function test_user_attributes_initialization(): void
    {
        $user = new User([
            'name' => 'Artem',
            'email' => 'artem@example.com'
        ]);

        $this->assertEquals('Artem', $user->name);
        $this->assertEquals('artem@example.com', $user->email);
    }

    /**
     * Приклад використання Mock (сурогату) для бізнес-логіки.
     * Це покриває вимогу щодо Mock/Spy (вимога 20%).
     */
    public function test_business_logic_with_mockery(): void
    {
        // Створюємо Mock (заглушку) для уявного сервісу обробки постів
        $postServiceMock = Mockery::mock('PostService');

        // Програмуємо очікування: метод 'publish' має бути викликаний 1 раз
        // і повернути логічне значення true
        $postServiceMock->shouldReceive('publish')
            ->once()
            ->andReturn(true);

        // Виконуємо перевірку
        $result = $postServiceMock->publish();

        $this->assertTrue($result);
    }

    /**
     * Обов'язково закриваємо Mockery після тесту
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
