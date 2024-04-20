<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as GithubUser;
use Mockery\MockInterface;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_redirect_success(): void
    {
        $response = $this->get(action([SocialAuthController::class, 'redirect'], ['driver' => 'github']));

        $this->assertGuest();
        $response->assertRedirectContains('https://github.com/login/oauth/authorize');
    }

    public function test_social_callback(): void
    {
        $githubUserEmail = 'github@mail.ru';

        $githubUser = $this->mock(GithubUser::class, function (MockInterface $mock) use ($githubUserEmail) {
            $mock->shouldReceive('getId')->andReturn(10);
            $mock->shouldReceive('getName')->andReturn('GitHub Name');
            $mock->shouldReceive('getEmail')->andReturn($githubUserEmail);
        });

        $githubProvider = $this->mock(GithubProvider::class, function (MockInterface $mock) use ($githubUser) {
            $mock->shouldReceive('user')->once()->andReturn($githubUser);
        });

        Socialite::shouldReceive('driver')->with('github')->andReturn($githubProvider);

        Event::fake();

        $this->assertGuest();

        $response = $this->get(action([SocialAuthController::class, 'callback'], ['driver' => 'github']));

        //        $this->assertDatabaseHas('users', [
        //            'email' => $githubUserEmail
        //        ]);

        //        $user = User::query()->where('email', $githubUserEmail)->first();

        //        $this->assertAuthenticatedAs($user);

        $this->assertAuthenticated();
        Event::assertDispatched(Login::class);

        $response->assertRedirect(route('home'));
    }
}
