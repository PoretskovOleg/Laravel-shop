<?php

declare(strict_types=1);

namespace Domain\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface LogoutUserContract
{
    public function __invoke(Authenticatable $user, ?string $guard = 'web'): void;
}
