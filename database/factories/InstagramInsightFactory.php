<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Enums\InstagramInsightType;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\InstagramInsight;

class InstagramInsightFactory extends Factory
{
    protected $model = InstagramInsight::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'type' => InstagramInsightType::IMPRESSIONS,
            'value' => $this->faker->numberBetween(),
            'date' => $this->faker->dateTimeBetween('-90 days'),
        ];
    }
}
