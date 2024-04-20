<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Contracts\LoginUserContract;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

final class LoginUserAction implements LoginUserContract
{
    public function __invoke(array $attemptData, ?string $guard = 'web'): bool
    {
        if (! Auth::guard($guard)->attempt($attemptData)) {
            return false;
        }

        request()->session()->regenerate();

        event(new Login($guard, auth()->user(), false));

        return true;
    }
}
