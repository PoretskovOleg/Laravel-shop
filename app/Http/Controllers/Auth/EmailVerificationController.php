<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function page(): View
    {
        return view('auth.verify-email');
    }

    public function verification(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        flash()->info('Ваша электронная почта подтверждена');

        return redirect()->route('home');
    }

    public function sendNotification(Request $request): RedirectResponse
    {
        $request->user()->sendEmailVerificationNotification();

        flash()->info('Verification link sent!');

        return back();
    }
}
