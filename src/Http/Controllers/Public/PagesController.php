<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Public;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Models\Page;

class PagesController extends Controller
{
    public function __invoke(?string $slug = null): View
    {
        $page = Page::where('status', true)->where('slug', $slug ?: 'home')->firstOrFail();

        return view('mixpost::public.page', [
            'page' => $page
        ]);
    }
}
