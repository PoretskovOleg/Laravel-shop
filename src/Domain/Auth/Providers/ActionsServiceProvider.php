<?php

namespace Domain\Auth\Providers;

use Domain\Auth\Actions\LoginUserAction;
use Domain\Auth\Actions\LogoutUserAction;
use Domain\Auth\Actions\RegisterUserAction;
use Domain\Auth\Contracts\LoginUserContract;
use Domain\Auth\Contracts\LogoutUserContract;
use Domain\Auth\Contracts\RegisterUserContract;
use Illuminate\Support\ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{
    public array $bindings = [
        LoginUserContract::class => LoginUserAction::class,
        LogoutUserContract::class => LogoutUserAction::class,
        RegisterUserContract::class => RegisterUserAction::class,
    ];
}
