<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Enums\PostStatus;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Util;

class StorePost extends PostFormRequest
{
    use UsesAuth;

    public function handle()
    {
        return DB::transaction(function () {
            $record = Post::create([
                'user_id' => self::getAuthGuard()->id(),
                'status' => PostStatus::DRAFT,
                'scheduled_at' => $this->scheduledAt() ? Util::convertTimeToUTC($this->scheduledAt()) : null
            ]);

            $record->accounts()->attach($this->input('accounts', []));
            $record->tags()->attach($this->input('tags'));
            $record->versions()->createMany($this->input('versions'));

            return $record;
        });
    }
}
