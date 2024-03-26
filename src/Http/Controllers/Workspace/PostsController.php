<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Actions\Post\RedirectAfterDeletedPost;
use SaguiAi\MixpostAdapter\Builders\Post\PostQuery;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\StorePost;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdatePost;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\Http\Resources\PostResource;
use SaguiAi\MixpostAdapter\Http\Resources\TagResource;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Models\Tag;
use SaguiAi\MixpostAdapter\PostingSchedule;

class PostsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|Response
    {
        $posts = PostQuery::apply($request)
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->onEachSide(1)
            ->withQueryString();

        return Inertia::render('Workspace/Posts/Index', [
            'accounts' => fn() => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => fn() => TagResource::collection(Tag::latest()->get())->resolve(),
            'filter' => [
                'keyword' => $request->query('keyword', ''),
                'status' => $request->query('status'),
                'tags' => $request->query('tags', []),
                'accounts' => $request->query('accounts', [])
            ],
            'posts' => fn() => PostResource::collection($posts)->additional([
                'filter' => [
                    'accounts' => Arr::map($request->query('accounts', []), 'intval')
                ]
            ]),
            'has_failed_posts' => Post::failed()->exists(),
            'service_configs' => Services::configs()
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Workspace/Posts/CreateEdit', [
            'default_accounts' => Settings::get('default_accounts'),
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::latest()->get())->resolve(),
            'has_available_times' => PostingSchedule::hasAvailableTimes(),
            'post' => null,
            'schedule_at' => [
                'date' => Str::before($request->route('schedule_at'), ' '),
                'time' => Str::after($request->route('schedule_at'), ' '),
            ],
            'prefill' => [
                'body' => $request->query('body', ''),
                'title' => $request->query('title', ''),
                'link' => $request->query('link', ''),
            ],
            'is_configured_service' => Services::isConfigured(),
            'service_configs' => Services::configs()
        ]);
    }

    public function store(StorePost $storePost): RedirectResponse
    {
        $post = $storePost->handle();

        return redirect()->route('mixpost.posts.edit', ['workspace' => $post->workspace->uuid, 'post' => $post->uuid]);
    }

    public function edit(Request $request): Response
    {
        $post = Post::firstOrFailTrashedByUuid($request->route('post'));

        $post->load('accounts', 'versions', 'tags');

        return Inertia::render('Workspace/Posts/CreateEdit', [
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::latest()->get())->resolve(),
            'has_available_times' => PostingSchedule::hasAvailableTimes(),
            'post' => new PostResource($post),
            'is_configured_service' => Services::isConfigured(),
            'service_configs' => Services::configs()
        ]);
    }

    public function update(UpdatePost $updatePost): HttpResponse
    {
        $updatePost->handle();

        return response()->noContent();
    }

    public function destroy(Request $request, RedirectAfterDeletedPost $redirectAfterPostDeleted): RedirectResponse
    {
        $query = Post::where('uuid', $request->route('post'));

        if ($request->get('status') === 'trash') {
            $query->forceDelete();
        }

        if ($request->get('status') !== 'trash') {
            $query->delete();
        }

        return $redirectAfterPostDeleted($request);
    }
}
