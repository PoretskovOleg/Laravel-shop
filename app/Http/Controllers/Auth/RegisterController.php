<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Domain\Auth\Contracts\RegisterUserContract;
use Domain\Auth\DTOs\RegisterUserDTO;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    public function form(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request, RegisterUserContract $action): RedirectResponse
    {
        $user = $action(RegisterUserDTO::fromRequest($request));

        auth()->login($user);

        return redirect()->route('verification.notice');
    }
}
