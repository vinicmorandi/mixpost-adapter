<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Builders\User\UserQuery;
use SaguiAi\MixpostAdapter\Http\Resources\UserResource;

class UserItemsController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $users = UserQuery::apply($request)->latest()->latest('id')->get();

        return UserResource::collection($users);
    }
}
