<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Http\Requests\Auth\SendPasswordResetLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController
{
    use UsesUserModel;

    public function create(): Response|RedirectResponse
    {
        if (!self::getUserClass()::exists()) {
            return redirect()->route('mixpost.installation');
        }

        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(SendPasswordResetLink $sendPasswordResetLink): RedirectResponse
    {
        $status = $sendPasswordResetLink->handle();

        return $status == Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
