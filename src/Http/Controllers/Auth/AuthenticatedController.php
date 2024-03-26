<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Pipeline;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Actions\Auth\AttemptToAuthenticate;
use SaguiAi\MixpostAdapter\Actions\Auth\EnsureLoginIsNotThrottled;
use SaguiAi\MixpostAdapter\Actions\Auth\PrepareAuthenticatedSession;
use SaguiAi\MixpostAdapter\Actions\Auth\RedirectIfTwoFactorAuthenticatable;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Features;
use SaguiAi\MixpostAdapter\Http\Requests\Auth\LoginRequest;

class AuthenticatedController extends Controller
{
    use UsesAuth;
    use UsesUserModel;

    public function create(): Response|RedirectResponse
    {
        if (!self::getUserClass()::exists()) {
            return redirect()->route('mixpost.installation');
        }

        return Inertia::render('Auth/Login', [
            'is_forgot_password_enabled' => Features::isForgotPasswordEnabled()
        ]);
    }

    public function store(LoginRequest $request)
    {
        return (new Pipeline(app()))->send($request)->through(array_filter([
            EnsureLoginIsNotThrottled::class,
            Features::isTwoFactorAuthEnabled() ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]))->then(function () {
            return redirect()->intended(route('mixpost.home'));
        });
    }

    public function destroy(Request $request): RedirectResponse
    {
        self::getAuthGuard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('mixpost.login');
    }
}
