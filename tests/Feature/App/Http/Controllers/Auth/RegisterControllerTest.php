<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\RegisterController;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_success(): void
    {
        $response = $this->get(action([RegisterController::class, 'form']));

        $this->assertGuest();

        $response
            ->assertOk()
            ->assertViewIs('auth.register')
            ->assertSee('Регистрация');
    }

    public function test_register_page_authenticated(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $response = $this->actingAs($user)->get(action([RegisterController::class, 'form']));

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_register_action_success(): void
    {
        Event::fake();
        Notification::fake();

        $requestData = [
            'name' => 'Fake Name',
            'email' => 'example@yandex.ru',
            'password' => 'PassWord123',
            'password_confirmation' => 'PassWord123',
        ];

        $this->assertDatabaseMissing('users', [
            'email' => $requestData['email'],
        ]);

        $response = $this->post(
            action([RegisterController::class, 'register']),
            $requestData
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $requestData['email'],
        ]);

        $user = User::query()->where('email', $requestData['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailVerificationNotification::class);

        $listener = new SendEmailVerificationNotification();
        $listener->handle(new Registered($user));
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_register_action_valid_error(): void
    {
        Event::fake();

        $requestData = [
            'name' => 'Fake Name',
            'email' => 'test@test.ru',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(
            action([RegisterController::class, 'register']),
            $requestData
        );

        $response->assertInvalid(['email', 'password']);
        $this->assertGuest();
        Event::assertNotDispatched(Registered::class);
        $response->assertRedirect(session()->previousUrl());
    }

    public function test_register_action_unique_user_error(): void
    {
        Event::fake();

        $requestData = [
            'name' => 'Fake Name',
            'email' => 'example@yandex.ru',
            'password' => 'PassWord123',
            'password_confirmation' => 'PassWord123',
        ];

        UserFactory::new()->count(1)->create([
            'email' => $requestData['email'],
            'password' => $requestData['password'],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $requestData['email'],
        ]);

        $response = $this->post(
            action([RegisterController::class, 'register']),
            $requestData
        );

        $response->assertValid(['password'])->assertInvalid(['email']);
        $this->assertGuest();
        Event::assertNotDispatched(Registered::class);
        $response->assertRedirect(session()->previousUrl());
    }
}
