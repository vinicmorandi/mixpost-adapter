<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\RedirectResponse;
use SaguiAi\MixpostAdapter\Http\Requests\Main\ConfirmPassword;

class ConfirmPasswordController
{
    public function __invoke(ConfirmPassword $confirmPassword): RedirectResponse
    {
        $confirmPassword->handle();

        return redirect()->back();
    }
}
