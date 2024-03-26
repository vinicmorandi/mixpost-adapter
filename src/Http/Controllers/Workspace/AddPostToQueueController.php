<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\AddPostToQueue;
use SaguiAi\MixpostAdapter\Util;

class AddPostToQueueController extends Controller
{
    public function __invoke(AddPostToQueue $addPostToQueue): JsonResponse
    {
        $scheduledPost = $addPostToQueue->handle();

        $scheduledAt = $scheduledPost->scheduled_at
            ->tz(Settings::get('timezone'))
            ->translatedFormat("D, M j, " . Util::timeFormat());

        return response()->json([
            'message' =>  __('mixpost::post.post_scheduled')."\n $scheduledAt",
            'date' => $scheduledPost->scheduled_at->toDateString(),
            'time' => $scheduledPost->scheduled_at->format('H:i'),
        ]);
    }
}
