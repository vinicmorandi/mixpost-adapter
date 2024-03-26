<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Http\Requests\Auth\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class NewPasswordController
{
    use UsesUserModel;

    public function create(Request $request): Response|RedirectResponse
    {
        if (!self::getUserClass()::exists()) {
            return redirect()->route('mixpost.installation');
        }

        return Inertia::render('Auth/ResetPassword', [
            'token' => $request->route('token'),
        ]);
    }

    public function store(ResetPassword $resetPassword): RedirectResponse
    {
        $status = $resetPassword->handle();

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('mixpost.login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
