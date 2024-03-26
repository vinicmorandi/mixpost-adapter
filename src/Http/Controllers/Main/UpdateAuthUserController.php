<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Main\UpdateAuthUser;

class UpdateAuthUserController extends Controller
{
    public function __invoke(UpdateAuthUser $updateUser): RedirectResponse
    {
        $updateUser->handle();

        return back();
    }
}
