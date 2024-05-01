<?php

declare(strict_types=1);

namespace Domain\Auth\Contracts;

use Domain\Auth\DTOs\RegisterUserDTO;
use Domain\Auth\Models\User;

interface RegisterUserContract
{
    public function __invoke(RegisterUserDTO $userDTO): User;
}
