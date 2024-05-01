<?php

declare(strict_types=1);

namespace Domain\Auth\DTOs;

use Illuminate\Http\Request;
use Support\Traits\Makeable;

final readonly class RegisterUserDTO
{
    use Makeable;

    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }

    public static function fromRequest(Request $request): RegisterUserDTO
    {
        return self::make(...$request->only('name', 'email', 'password'));
    }
}
