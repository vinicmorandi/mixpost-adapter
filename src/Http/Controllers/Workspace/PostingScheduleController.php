<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\UpdatePostingSchedule;
use SaguiAi\MixpostAdapter\PostingSchedule;

class PostingScheduleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Workspace/PostingSchedule', [
            'times' => fn() => PostingSchedule::timesUserTimezone()
        ]);
    }

    public function update(UpdatePostingSchedule $updatePostingSchedule): RedirectResponse
    {
        $updatePostingSchedule->handle();

        return back();
    }
}
