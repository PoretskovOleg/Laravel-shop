<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Contracts\RegisterUserContract;
use Domain\Auth\DTOs\RegisterUserDTO;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;

final class RegisterUserAction implements RegisterUserContract
{
    public function __invoke(RegisterUserDTO $userDTO): User
    {
        $user = User::query()->create([
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $userDTO->password,
        ]);

        event(new Registered($user));

        return $user;
    }
}
