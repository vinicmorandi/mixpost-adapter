<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\StoreVariable;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdateVariable;
use SaguiAi\MixpostAdapter\Http\Resources\VariableResource;
use SaguiAi\MixpostAdapter\Models\Variable;

class VariablesController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $records = Variable::latest()->latest('id')->get();

        return VariableResource::collection($records);
    }

    public function store(StoreVariable $storeVariable): JsonResponse
    {
        $record = $storeVariable->handle();

        return response()->json($record);
    }

    public function update(UpdateVariable $updateVariable): JsonResponse
    {
        $record = $updateVariable->handle();

        return response()->json($record);
    }

    public function destroy(Request $request): JsonResponse
    {
        $deleteRecord = Variable::where('uuid', $request->route('variable'))->delete();

        return response()->json($deleteRecord);
    }
}
