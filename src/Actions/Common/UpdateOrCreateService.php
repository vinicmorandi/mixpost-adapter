<?php

namespace SaguiAi\MixpostAdapter\Actions\Common;

use SaguiAi\MixpostAdapter\Models\Service;

class UpdateOrCreateService
{
    public function __invoke(string $name, array $value): Service
    {
        return Service::updateOrCreate(['name' => $name], [
            'credentials' => $value
        ]);
    }
}
