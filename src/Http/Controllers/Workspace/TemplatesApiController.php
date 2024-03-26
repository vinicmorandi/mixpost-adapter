<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\StoreTemplate;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdateTemplate;
use SaguiAi\MixpostAdapter\Http\Resources\TemplateResource;
use SaguiAi\MixpostAdapter\Models\Template;

class TemplatesApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $records = Template::latest()->latest('id')->get();

        return TemplateResource::collection($records);
    }

    public function store(StoreTemplate $storeTemplate): TemplateResource
    {
        $record = $storeTemplate->handle();

        return new TemplateResource($record);
    }

    public function update(UpdateTemplate $updateTemplate): JsonResponse
    {
        $record = $updateTemplate->handle();

        return response()->json($record);
    }

    public function destroy(Request $request): JsonResponse
    {
        $deleteRecord = Template::where('uuid', $request->route('template'))->delete();

        return response()->json($deleteRecord);
    }
}
