<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Audience;
use SaguiAi\MixpostAdapter\Models\Workspace;

class AudienceFactory extends Factory
{
    protected $model = Audience::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory(),
            'account_id' => Account::factory(),
            'total' => $this->faker->numberBetween(1, 100000),
            'date' => $this->faker->dateTimeBetween('-90 days'),
        ];
    }
}
