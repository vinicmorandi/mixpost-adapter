<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Features;
use SaguiAi\MixpostAdapter\Support\TimezoneList;
use SaguiAi\MixpostAdapter\Util;

class ProfileController extends Controller
{
    use UsesAuth;

    public function index(): Response
    {
        return Inertia::render('Main/Profile', [
            'settings' => Settings::all(),
            'locales' => Util::config('locales'),
            'timezone_list' => (new TimezoneList())->splitGroup()->list(),
            'user_has_two_factor_auth_enabled' => self::getAuthGuard()->user()->hasTwoFactorAuthEnabled(),
            'is_two_factor_auth_enabled' => Features::isTwoFactorAuthEnabled()
        ]);
    }
}
