<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use DomainException;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $driver): RedirectResponse
    {
        try {
            return Socialite::driver($driver)->redirect();
        } catch (Throwable) {
            throw new DomainException('Произошла ошибка или драйвер не поддерживается');
        }
    }

    public function callback(string $driver): RedirectResponse
    {
        if ($driver !== 'github') {
            throw new DomainException('Драйвер не поддерживается');
        }

        $driverUser = Socialite::driver($driver)->user();
        $driverUserEmail = str($driverUser->getEmail())->squish()->lower()->value();

        $user = User::query()->updateOrCreate([
            $driver.'_id' => $driverUser->getId(),
        ], [
            'name' => $driverUser->getName() ?? $driverUserEmail,
            'email' => $driverUserEmail,
            'password' => Str::random(),
        ]);

        auth()->login($user);

        event(new Login('web', $user, false));

        return redirect()->intended(route('home'));
    }
}
