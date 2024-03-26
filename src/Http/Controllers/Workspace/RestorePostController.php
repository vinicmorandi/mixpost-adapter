<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Models\Post;

class RestorePostController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $post = Post::firstOrFailTrashedByUuid($request->route('post'));

        $post->restore();

        return redirect()->back();
    }
}
