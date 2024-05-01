<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Actions;

use Domain\Auth\Contracts\RegisterUserContract;
use Domain\Auth\DTOs\RegisterUserDTO;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_user_created(): void
    {
        Event::fake();
        $action = app(RegisterUserContract::class);
        $userEmail = 'test@email.ru';

        $this->assertDatabaseMissing('users', ['email' => $userEmail]);

        $response = $action(RegisterUserDTO::make('test', $userEmail, 'password123'));

        Event::assertDispatched(Registered::class);

        $this->assertDatabaseHas('users', ['email' => $userEmail]);
        $this->assertInstanceOf(User::class, $response);
    }
}
