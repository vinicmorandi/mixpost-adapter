<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Models\Workspace;

class DashboardController extends Controller
{
    use UsesUserModel;

    public function __invoke()
    {
        return Inertia::render('Admin/Dashboard', [
            'count' => [
                'users' => self::getUserClass()::count(),
                'workspaces' => Workspace::count(),
            ],
            'is_configured_service' => Services::isConfigured(),
        ]);
    }
}
