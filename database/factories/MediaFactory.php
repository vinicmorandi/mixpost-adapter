<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Models\Workspace;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition()
    {
        $size = $this->faker->randomDigit();

        return [
            'uuid' => $this->faker->uuid,
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->domainName,
            'mime_type' => $this->faker->mimeType(),
            'disk' => 'public',
            'path' => '',
            'size' => $size,
            'size_total' => $size,
            'conversions' => []
        ];
    }
}
