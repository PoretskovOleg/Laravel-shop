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
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $requestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestData = [
            'name' => 'Fake Name',
            'email' => 'example@yandex.ru',
            'password' => 'PassWord123',
            'password_confirmation' => 'PassWord123',
        ];
    }

    public function test_register_page_success(): void
    {
        $this->get(
            action([RegisterController::class, 'form'])
        )
            ->assertOk()
            ->assertViewIs('auth.register')
            ->assertSee('Регистрация');

        $this->assertGuest();
    }

    public function test_register_page_authenticated(): void
    {
        $user = UserFactory::new()->count(1)->create()->first();

        $this->actingAs($user)
            ->get(
                action([RegisterController::class, 'form'])
            )
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticated();
    }

    public function test_register_action_validate_name(): void
    {
        $this->requestData['name'] = null;

        $this->actionRegister($this->requestData)
            ->assertValid(['email', 'password'])
            ->assertInvalid('name');
    }

    public function test_register_action_validate_email(): void
    {
        $this->requestData['email'] = 'test@test.ru';

        $this->actionRegister($this->requestData)
            ->assertValid(['name', 'password'])
            ->assertInvalid('email');
    }

    public function test_register_action_validate_email_exist(): void
    {
        UserFactory::new()->count(1)->create([
            'email' => $this->requestData['email'],
        ]);

        $this->actionRegister($this->requestData)
            ->assertValid(['name', 'password'])
            ->assertInvalid('email');
    }

    public function test_register_action_validate_password(): void
    {
        $this->requestData['password'] = 'PassWord';
        $this->requestData['password_confirmation'] = 'PassWord';

        $this->actionRegister($this->requestData)
            ->assertValid(['name', 'email'])
            ->assertInvalid('password');
    }

    public function test_register_action_validate_password_confirmation(): void
    {
        $this->requestData['password'] = 'PassWord123';
        $this->requestData['password_confirmation'] = 'PassWord321';

        $this->actionRegister($this->requestData)
            ->assertValid(['name', 'email'])
            ->assertInvalid('password');
    }

    public function test_register_action_success(): void
    {
        Event::fake();

        $this->assertDatabaseMissing('users', [
            'email' => $this->requestData['email'],
        ]);

        $response = $this->actionRegister($this->requestData)
            ->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $this->requestData['email'],
        ]);

        $user = User::query()->where('email', $this->requestData['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailVerificationNotification::class);

        $listener = new SendEmailVerificationNotification();
        $listener->handle(new Registered($user));
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('verification.notice'));
    }

    private function actionRegister(array $requestData): TestResponse
    {
        return $this->post(
            action([RegisterController::class, 'register']),
            $requestData
        );
    }
}
