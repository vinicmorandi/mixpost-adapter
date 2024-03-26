<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\TemplateResource;
use SaguiAi\MixpostAdapter\Models\Template;

class TemplatesController extends Controller
{
    public function index(): Response
    {
        $templates = Template::latest()->latest('id')->get();

        return Inertia::render('Workspace/Templates/Index', [
            'templates' => fn() => TemplateResource::collection($templates)->resolve()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Workspace/Templates/CreateEdit', [
            'template' => null,
            'is_configured_service' => Services::isConfigured(),
        ]);
    }

    public function edit(Request $request): Response
    {
        $template = Template::firstOrFailByUuid($request->route('template'));

        return Inertia::render('Workspace/Templates/CreateEdit', [
            'template' => new TemplateResource($template),
            'is_configured_service' => Services::isConfigured(),
        ]);
    }
}
