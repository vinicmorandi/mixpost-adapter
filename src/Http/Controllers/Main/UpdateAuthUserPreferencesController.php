<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Main\SaveSettings;

class UpdateAuthUserPreferencesController extends Controller
{
    public function __invoke(SaveSettings $saveSettings): RedirectResponse
    {
        $saveSettings->handle();

        return back();
    }
}
