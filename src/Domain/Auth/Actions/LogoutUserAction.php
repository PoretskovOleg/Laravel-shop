<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Contracts\LogoutUserContract;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;

final class LogoutUserAction implements LogoutUserContract
{
    public function __invoke(Authenticatable $user, ?string $guard = 'web'): void
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        event(new Logout($guard, $user));
    }
}
