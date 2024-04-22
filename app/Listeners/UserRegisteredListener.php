<?php

namespace App\Listeners;

use App\Notifications\UserRegisteredNotification;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Verified;

class UserRegisteredListener
{
    public function handle(Verified $event): void
    {
        if ($event->user instanceof User) {
            $event->user->notify(new UserRegisteredNotification());
        }
    }
}
