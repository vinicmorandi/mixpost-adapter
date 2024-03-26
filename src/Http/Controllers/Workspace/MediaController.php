<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\DeleteMedia;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Workspace/Media', [
            'is_configured_service' => Services::isConfigured()
        ]);
    }

    public function destroy(DeleteMedia $deleteMediaFiles): HttpResponse
    {
        $deleteMediaFiles->handle();

        return response()->noContent();
    }
}
