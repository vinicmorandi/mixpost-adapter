<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Jobs\DeleteWorkspaceDataJob;
use SaguiAi\MixpostAdapter\Models\Workspace;

class DeleteWorkspacesController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Workspace::select(['id', 'uuid'])->whereIn('uuid', $request->input('workspaces', []))
            ->get()
            ->each(function ($workspace) {
                DeleteWorkspaceDataJob::dispatch($workspace->id, $workspace->uuid);
            });

        Workspace::whereIn('uuid', $request->input('workspaces', []))->delete();

        return redirect()->back();
    }
}
