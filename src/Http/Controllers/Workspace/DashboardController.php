<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\Models\Account;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Workspace/Dashboard', [
            'accounts' => fn() => AccountResource::collection(Account::oldest()->get())->resolve()
        ]);
    }
}
