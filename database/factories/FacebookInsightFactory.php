<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Enums\FacebookInsightType;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\FacebookInsight;
use SaguiAi\MixpostAdapter\Models\Workspace;

class FacebookInsightFactory extends Factory
{
    protected $model = FacebookInsight::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory(),
            'account_id' => Account::factory()->state([
                'provider' => 'facebook_page'
            ]),
            'type' => FacebookInsightType::PAGE_POSTS_IMPRESSIONS,
            'value' => $this->faker->randomDigit(),
            'date' => $this->faker->dateTimeBetween('-90 days')
        ];
    }
}
