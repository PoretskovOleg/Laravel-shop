<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Contracts\RegisterUserContract;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;

final class RegisterUserAction implements RegisterUserContract
{
    public function __invoke(array $userData): void
    {
        $user = User::query()->create($userData);

        event(new Registered($user));

        auth()->login($user);
    }
}
