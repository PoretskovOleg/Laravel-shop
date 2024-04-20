<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_page_success(): void
    {
        $this->assertGuest();

        $response = $this->get(
            action([ResetPasswordController::class, 'form'], ['token' => Str::random(20)]),
        );

        $this->assertGuest();

        $response
            ->assertOk()
            ->assertViewIs('auth.reset-password')
            ->assertSee('Восстановление пароля');
    }

    public function test_reset_password_page_authenticated(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $response = $this->actingAs($user)
            ->get(
                action([ResetPasswordController::class, 'form'], ['token' => Str::random(20)]),
            );

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_reset_password_action_success(): void
    {
        Event::fake();

        /** @var User $user */
        $user = UserFactory::new()->count(1)->create([
            'email' => 'example@yandex.ru',
            'password' => 'Pass123Word',
        ])->first();

        $token = Password::createToken($user);
        $user->setRememberToken($token);
        $user->save();

        $newPassword = 'PassWord321';
        $requestData = [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ];
        $response = $this->post(
            action([ResetPasswordController::class, 'resetPassword']),
            $requestData
        );

        $user->refresh();

        $response->assertValid();
        $this->assertGuest();
        $this->assertTrue(Hash::check($newPassword, $user->getAuthPassword()));
        Event::assertDispatched(PasswordReset::class);
        $response->assertRedirect(route('login'));
    }

    public function test_reset_password_action_token_error(): void
    {
        Event::fake();

        /** @var User $user */
        $user = UserFactory::new()->count(1)->create([
            'email' => 'example@yandex.ru',
            'password' => 'Pass123Word',
        ])->first();

        $token = Str::random(20);

        $newPassword = 'PassWord321';
        $requestData = [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ];

        $response = $this->post(
            action([ResetPasswordController::class, 'resetPassword']),
            $requestData
        );
        $user->refresh();

        $response->assertInvalid(['email']);
        $this->assertGuest();
        $this->assertFalse(Hash::check($newPassword, $user->getAuthPassword()));
        Event::assertNotDispatched(PasswordReset::class);
        $response->assertRedirect(session()->previousUrl());
    }
}
