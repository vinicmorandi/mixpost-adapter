<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Resources\WorkspaceResource;
use SaguiAi\MixpostAdapter\Builders\Workspace\WorkspaceQuery;

class WorkspaceItemsController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $workspaces = WorkspaceQuery::apply($request)->latest()->latest('id')->get();

        return WorkspaceResource::collection($workspaces);
    }
}
