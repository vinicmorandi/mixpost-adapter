<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Http\Requests\Auth\InstallationRequest;
use SaguiAi\MixpostAdapter\Support\TimezoneList;

class InstallationController extends Controller
{
    use UsesAuth;
    use UsesUserModel;

    public function create(): Response|RedirectResponse
    {
        if (self::getUserClass()::exists()) {
            return redirect()->route('mixpost.login');
        }

        return Inertia::render('Auth/Installation', [
            'timezone_list' => (new TimezoneList())->splitGroup()->list(),
        ]);
    }

    public function store(InstallationRequest $request): RedirectResponse
    {
        $request->handle();

        return redirect()->route('mixpost.workspaces.create');
    }
}
