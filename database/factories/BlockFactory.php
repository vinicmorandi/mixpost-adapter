<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Enums\ResourceStatus;
use SaguiAi\MixpostAdapter\Models\Block;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'module' => 'Editor',
            'content' => [
                'body' => '<p>' . $this->faker->paragraph . '</p>'
            ],
            'status' => ResourceStatus::ENABLED
        ];
    }
}
