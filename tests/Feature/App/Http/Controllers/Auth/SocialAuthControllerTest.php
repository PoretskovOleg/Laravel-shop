<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\SocialAuthController;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use DomainException;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Mockery\MockInterface;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_redirect_success(): void
    {
        $response = $this->get(
            action(
                [SocialAuthController::class, 'redirect'],
                ['driver' => 'github']
            )
        );

        $this->assertGuest();
        $response->assertRedirectContains('https://github.com/login/oauth/authorize');
    }

    public function test_social_exception(): void
    {
        $this->expectException(DomainException::class);

        $this->withoutExceptionHandling()->get(
            action(
                [SocialAuthController::class, 'callback'],
                ['driver' => 'vk']
            )
        );
    }

    public function test_social_callback(): void
    {
        $githubId = Str::random(10);

        $this->mockingSocialite($githubId);

        Event::fake();

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'github_id' => $githubId
        ]);

        $response = $this->get(
            action(
                [SocialAuthController::class, 'callback'],
                ['driver' => 'github']
            )
        );

        $this->assertDatabaseHas('users', [
            'github_id' => $githubId
        ]);

        $user = User::query()->where('github_id', $githubId)->first();

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(Login::class);

        $response->assertRedirect(route('home'));
    }

    public function test_social_callback_exist_user(): void
    {
        $githubId = Str::random(10);

        $this->mockingSocialite($githubId);

        $user = UserFactory::new()->count(1)->create([
            'github_id' => $githubId
        ])->first();

        Event::fake();

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'github_id' => $githubId
        ]);

        $response = $this->get(
            action(
                [SocialAuthController::class, 'callback'],
                ['driver' => 'github']
            )
        );

        $countUsers = User::query()->where('github_id', $githubId)->count();
        $this->assertEquals(1, $countUsers);

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(Login::class);

        $response->assertRedirect(route('home'));
    }

    private function mockingSocialite(string $githubId): void
    {
        $githubUser = $this->mock(SocialiteUser::class, function (MockInterface $mock) use ($githubId) {
            $mock->shouldReceive('getId')->once()->andReturn($githubId);
            $mock->shouldReceive('getName')->once()->andReturn(Str::random());
            $mock->shouldReceive('getEmail')->once()->andReturn('github@yandex.ru');
        });

        $githubProvider = $this->mock(GithubProvider::class, function (MockInterface $mock) use ($githubUser) {
            $mock->shouldReceive('user')->once()->andReturn($githubUser);
        });

        Socialite::shouldReceive('driver')->andReturn($githubProvider);
    }
}
