<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\StoreTag;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdateTag;
use SaguiAi\MixpostAdapter\Models\Tag;

class TagsController extends Controller
{
    public function store(StoreTag $storeTag): RedirectResponse
    {
        $storeTag->handle();

        return redirect()->back();
    }

    public function update(UpdateTag $updateTag): RedirectResponse
    {
        $updateTag->handle();

        return redirect()->back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        Tag::where('uuid', $request->route('tag'))->delete();

        return redirect()->back();
    }
}
