<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Actions\Post\RedirectAfterDeletedPost;
use SaguiAi\MixpostAdapter\Models\Post;

class DeletePostsController extends Controller
{
    public function __invoke(Request $request, RedirectAfterDeletedPost $redirectAfterPostDeleted): RedirectResponse
    {
        $query = Post::whereIn('uuid', $request->input('posts'));

        if ($request->get('status') === 'trash') {
            $query->forceDelete();
        }

        if ($request->get('status') !== 'trash') {
            $query->delete();
        }

        return $redirectAfterPostDeleted($request);
    }
}
