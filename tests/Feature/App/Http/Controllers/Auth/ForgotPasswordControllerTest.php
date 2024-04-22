<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_success(): void
    {
        $response = $this->get(action([ForgotPasswordController::class, 'form']));

        $this->assertGuest();

        $response
            ->assertOk()
            ->assertViewIs('auth.forgot-password')
            ->assertSee('Забыли пароль');
    }

    public function test_forgot_password_page_authenticated(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $response = $this->actingAs($user)->get(action([ForgotPasswordController::class, 'form']));

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_forgot_password_action_success(): void
    {
        $userEmail = 'example@yandex.ru';
        /** @var User $user */
        $user = UserFactory::new()->count(1)->create([
            'email' => $userEmail,
        ])->first();

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'email' => $userEmail,
        ]);

        $response = $this->post(
            action([ForgotPasswordController::class, 'forgotPassword']),
            ['email' => $userEmail]
        );

        $response->assertValid();
        $this->assertGuest();
        Notification::assertSentTo($user, ResetPassword::class);
        $response->assertRedirect(session()->previousUrl());
        $this->assertEquals(__(Password::RESET_LINK_SENT), flash()->get()->message());
    }

    public function test_forgot_password_action_error(): void
    {
        $response = $this->post(
            action([ForgotPasswordController::class, 'forgotPassword']),
            ['email' => 'example@yandex.ru']
        );

        $response->assertValid();
        $this->assertGuest();
        $response->assertRedirect(session()->previousUrl());
        $this->assertEquals(__(Password::RESET_LINK_SENT), flash()->get()->message());
        Notification::assertNothingSent();
    }
}
