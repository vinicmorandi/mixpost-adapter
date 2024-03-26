<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Enums\PostStatus;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Post;

class DuplicatePostController extends Controller
{
    use UsesAuth;

    public function __invoke(Request $request): RedirectResponse
    {
        $post = Post::firstOrFailTrashedByUuid($request->route('post'));

        DB::transaction(function () use ($post) {
            $newPost = Post::create([
                'user_id' => self::getAuthGuard()->id(),
                'status' => PostStatus::DRAFT
            ]);

            $newPost->accounts()->attach($post->accounts->pluck('id'));
            $newPost->tags()->attach($post->tags->pluck('id'));
            $newPost->versions()->createMany($post->versions->map(function ($version) {
                return [
                    'account_id' => $version->account_id,
                    'is_original' => $version->is_original,
                    'content' => $version->content,
                    'options' => $version->options,
                ];
            })->toArray());
        });

        return redirect()->route('mixpost.posts.index', ['workspace' => WorkspaceManager::current()->uuid]);
    }
}
