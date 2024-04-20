<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Listeners\UserRegisteredListener;
use App\Notifications\UserRegisteredNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_page_success(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $response = $this->actingAs($user)->get(action([EmailVerificationController::class, 'page']));

        $this->assertAuthenticatedAs($user);

        $response
            ->assertOk()
            ->assertViewIs('auth.verify-email')
            ->assertSee('Необходимо подтвердить e-mail');
    }

    public function test_email_verification_page_unauthenticated(): void
    {
        $this->assertGuest();

        $response = $this->get(action([EmailVerificationController::class, 'page']));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_email_verification_action_success(): void
    {
        Event::fake();
        Notification::fake();

        /** @var User $user */
        $user = UserFactory::new()->count(1)->create(['email_verified_at' => null])->first();
        $this->assertEmpty($user->email_verified_at);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNotEmpty($user->email_verified_at);

        Event::assertDispatched(Verified::class);
        Event::assertListening(Verified::class, UserRegisteredListener::class);

        $listener = new UserRegisteredListener();
        $listener->handle(new Verified($user));
        Notification::assertSentTo($user, UserRegisteredNotification::class);

        $response->assertRedirect(route('home'));
        $this->assertEquals('Ваша электронная почта подтверждена', flash()->get()->message());
    }

    public function test_email_verification_send_success(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = UserFactory::new()->count(1)->create(['email_verified_at' => null])->first();

        $response = $this->actingAs($user)->post(action([EmailVerificationController::class, 'sendNotification']));

        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect(session()->previousUrl());
        $this->assertEquals('Verification link sent!', flash()->get()->message());
    }
}
