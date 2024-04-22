<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Domain\Auth\Contracts\LoginUserContract;
use Domain\Auth\Contracts\LogoutUserContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function form(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request, LoginUserContract $action): RedirectResponse
    {
        if (! $action($request->only('email', 'password'))) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('email');
        }

        return redirect()->intended(route('home'));
    }

    public function logout(LogoutUserContract $action): RedirectResponse
    {
        $action(auth()->user());

        return redirect()->intended(route('home'));
    }
}
