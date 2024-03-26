<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\SchedulePost;
use SaguiAi\MixpostAdapter\Util;

class SchedulePostController extends Controller
{
    public function __invoke(SchedulePost $schedulePost): JsonResponse
    {
        $schedulePost->handle();

        $scheduledAt = $schedulePost->getDateTime()->tz(Settings::get('timezone'))->translatedFormat("D, M j, " . Util::timeFormat());

        return response()->json(__('mixpost::post.post_scheduled')."\n $scheduledAt");
    }
}
