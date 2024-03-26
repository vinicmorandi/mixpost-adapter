<?php

namespace SaguiAi\MixpostAdapter\BlockModules;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use SaguiAi\MixpostAdapter\Models\Block;

class Html
{
    public function render(Block $block): Factory|View|Application
    {
        return view('mixpost::blocks_modules.Html', [
            'block' => $block
        ]);
    }
}
