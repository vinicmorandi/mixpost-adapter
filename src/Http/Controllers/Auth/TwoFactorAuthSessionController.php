<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Http\Requests\Auth\TwoFactorLoginRequest;

class TwoFactorAuthSessionController extends Controller
{
    use UsesAuth;

    public function create(TwoFactorLoginRequest $request): Response|RedirectResponse
    {
        if (!$request->hasChallengedUser()) {
            return redirect()->route('mixpost.login');
        }

        return Inertia::render('Auth/TwoFactorLogin');
    }

    public function store(TwoFactorLoginRequest $request): RedirectResponse
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->twoFactorReplaceRecoveryCode($code);
        } elseif (!$request->hasValidCode()) {
            return redirect()
                ->back()
                ->withErrors(['code' => __('mixpost::auth.provided_two_factor_code_invalid')]);
        }

        self::getAuthGuard()->login($user, $request->remember());

        $request->session()->regenerate();

        return redirect()->intended(route('mixpost.home'));
    }
}
