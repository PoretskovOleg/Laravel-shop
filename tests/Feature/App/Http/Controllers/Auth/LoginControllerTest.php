<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_success(): void
    {
        $this->get(action([LoginController::class, 'form']))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Вход в аккаунт');

        $this->assertGuest();
    }

    public function test_login_page_authenticated(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $this->actingAs($user)
            ->get(action([LoginController::class, 'form']))
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticated();
    }

    public function test_login_action_validate_email(): void
    {
        $requestData = [
            'email' => 'test@test.ru',
            'password' => 'PassWord123',
        ];

        $this->loginAction($requestData)
            ->assertValid('password')
            ->assertInvalid('email');
    }

    public function test_login_action_validate_password(): void
    {
        $requestData = [
            'email' => 'test@yandex.ru',
            'password' => 'PassWord',
        ];

        $this->loginAction($requestData)
            ->assertValid('email')
            ->assertInvalid('password');
    }

    public function test_login_action_success(): void
    {
        $requestData = [
            'email' => 'example@yandex.ru',
            'password' => 'PassWord123',
        ];

        Event::fake();

        /** @var User $user */
        $user = UserFactory::new()->count(1)->create($requestData)->first();

        $this->assertGuest();

        $response = $this->loginAction($requestData)
            ->assertValid();

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(Login::class);

        $response->assertRedirect(route('home'));
    }

    public function test_login_action_error(): void
    {
        $requestData = [
            'email' => 'example@yandex.ru',
            'password' => 'PassWord123',
        ];

        Event::fake();

        $this->assertGuest();

        $response = $this->loginAction($requestData)
            ->assertValid('password')
            ->assertInvalid(['email' => 'The provided credentials do not match our records']);

        $this->assertGuest();

        Event::assertNotDispatched(Login::class);

        $response->assertRedirect(session()->previousUrl());
    }

    public function test_logout_action(): void
    {
        Event::fake();
        $user = UserFactory::new()->count(1)->create()->first();

        $response = $this->actingAs($user)
            ->delete(
                action([LoginController::class, 'logout'])
            );

        $this->assertGuest();
        Event::assertDispatched(Logout::class);
        $response->assertRedirect(route('home'));
    }

    private function loginAction(array $requestData): TestResponse
    {
        return $this->post(
            action([LoginController::class, 'login']),
            $requestData
        );
    }
}
