<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\AttachWorkspaceUser;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\DetachWorkspaceUser;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\UpdateWorkspaceUserRole;

class WorkspaceUsersController extends Controller
{
    public function store(AttachWorkspaceUser $addWorkspaceUser): RedirectResponse
    {
        $addWorkspaceUser->handle();

        return redirect()->back();
    }

    public function update(UpdateWorkspaceUserRole $updateWorkspaceUserRole): RedirectResponse
    {
        $updateWorkspaceUserRole->handle();

        return redirect()->back();
    }

    public function destroy(DetachWorkspaceUser $detachWorkspaceUser): RedirectResponse
    {
        $detachWorkspaceUser->handle();

        return redirect()->back();
    }
}
