<?php

declare(strict_types=1);

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

final class AuthRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            $this->getRegistrarGuest();
            $this->getRegistrarAuth();
        });
    }

    private function getRegistrarGuest(): void
    {
        Route::middleware('guest')->group(function () {
            Route::get('login', [LoginController::class, 'form'])
                ->name('login');
            Route::post('login', [LoginController::class, 'login'])
                ->middleware('throttle:auth')
                ->name('login');

            Route::get('register', [RegisterController::class, 'form'])
                ->name('register');
            Route::post('register', [RegisterController::class, 'register'])
                ->middleware('throttle:auth')
                ->name('register');

            Route::get('forgot-password', [ForgotPasswordController::class, 'form'])
                ->name('password.forgot');
            Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword'])
                ->name('password.forgot');

            Route::get('reset-password/{token}', [ResetPasswordController::class, 'form'])
                ->name('password.reset');
            Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])
                ->name('password.update');

            Route::get('auth/socialite/{driver}', [SocialAuthController::class, 'redirect'])
                ->name('socialite.redirect');
            Route::get('auth/socialite/{driver}/callback', [SocialAuthController::class, 'callback'])
                ->name('socialite.callback');
        });
    }

    private function getRegistrarAuth(): void
    {
        Route::middleware('auth')->group(function () {
            Route::delete('logout', [LoginController::class, 'logout'])
                ->name('logout');

            Route::get('email/verify', [EmailVerificationController::class, 'page'])
                ->name('verification.notice');

            Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verification'])
                ->middleware('signed')
                ->name('verification.verify');

            Route::post(
                'email/verification-notification',
                [EmailVerificationController::class, 'sendNotification']
            )
                ->middleware('throttle:auth')
                ->name('verification.send');
        });
    }
}
