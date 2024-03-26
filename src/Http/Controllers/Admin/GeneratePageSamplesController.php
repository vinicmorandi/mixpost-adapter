<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\GeneratePageSamples;

class GeneratePageSamplesController extends Controller
{
    public function __invoke(GeneratePageSamples $generatePageSamples): RedirectResponse
    {
        $generatePageSamples->handle();

        return redirect()->back();
    }
}
