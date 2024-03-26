<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Facades\Settings;

class DeleteUsersController extends Controller
{
    use UsesAuth;
    use UsesUserModel;

    public function __invoke(Request $request): RedirectResponse
    {
        $ids = Arr::where($request->input('users'), function ($id) {
            return $id !== self::getAuthGuard()->id();
        });

        self::getUserClass()::whereIn('id', $ids)->delete();

        collect($ids)->each(function ($id) {
            Settings::forgetAll($id);
        });

        return redirect()->back();
    }
}
