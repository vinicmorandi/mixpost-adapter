<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Mixpost;

class HomeController extends Controller
{
    use UsesAuth;
    use UsesUserModel;

    public function __invoke(): RedirectResponse
    {
        $workspace = self::getAuthGuard()->user()->getActiveWorkspace();

        if (!$workspace) {
            $workspace = self::getAuthGuard()->user()->workspaces()->recentlyUpdated()->first();

            // If there is a recently updated workspace, set it as user active workspace
            if ($workspace) {
                self::getAuthGuard()->user()->setActiveWorkspace($workspace);
            }
        }

        if (!$workspace) {
            if (self::getAuthGuard()->user()->isAdmin()) {
                return redirect()->to(route('mixpost.workspaces.create'));
            }

            if ($createWorkspaceUrl = Mixpost::getCreateWorkspaceUrl()) {
                return redirect()->to($createWorkspaceUrl);
            }

            abort(403);
        }

        return redirect()->to(route('mixpost.dashboard', ['workspace' => $workspace->uuid]));
    }
}
