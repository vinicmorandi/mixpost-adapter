<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Variable;
use SaguiAi\MixpostAdapter\Models\Workspace;

class VariableFactory extends Factory
{
    protected $model = Variable::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->domainName,
            'value' => $this->faker->paragraph
        ];
    }
}
