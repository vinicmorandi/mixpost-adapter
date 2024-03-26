<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\StoreBlock;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\UpdateBlock;
use SaguiAi\MixpostAdapter\Models\Block;

class BlocksController extends Controller
{
    use UsesUserModel;

    public function store(StoreBlock $storeBlock): RedirectResponse
    {
        $storeBlock->handle();

        return redirect()->back();
    }

    public function update(UpdateBlock $updateBlock): RedirectResponse
    {
        $updateBlock->handle();

        return redirect()->back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $block = Block::where('id', $request->route('block'))->firstOrFail();

        $block->delete();

        return redirect()->back();
    }
}
