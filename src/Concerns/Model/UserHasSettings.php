<?php

namespace SaguiAi\MixpostAdapter\Concerns\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use SaguiAi\MixpostAdapter\Models\Setting;

trait UserHasSettings
{
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'user_id');
    }
}
