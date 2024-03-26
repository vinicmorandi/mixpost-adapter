<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdateAccountSuffix;

class UpdateAccountSuffixController extends Controller
{
    public function __invoke(UpdateAccountSuffix $updateAccountSuffix): RedirectResponse
    {
        $updateAccountSuffix->handle();

        return redirect()->back();
    }
}
