<?php

declare(strict_types=1);

namespace Domain\Auth\Contracts;

interface RegisterUserContract
{
    public function __invoke(array $userData): void;
}
