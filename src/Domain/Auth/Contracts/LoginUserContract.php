<?php

declare(strict_types=1);

namespace Domain\Auth\Contracts;

interface LoginUserContract
{
    public function __invoke(array $attemptData, ?string $guard = 'web'): bool;
}
