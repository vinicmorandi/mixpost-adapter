<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\PostingSchedule;
use SaguiAi\MixpostAdapter\Models\Workspace;

class PostingScheduleFactory extends Factory
{
    protected $model = PostingSchedule::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory(),
            'times' => [],
        ];
    }
}
