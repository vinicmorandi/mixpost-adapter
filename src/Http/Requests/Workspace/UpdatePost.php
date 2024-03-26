<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Util;

class UpdatePost extends PostFormRequest
{
    public Post $post;

    public function withValidator($validator)
    {
        $this->post = Post::firstOrFailByUuid($this->route('post'));

        $validator->after(function ($validator) {
            if ($this->post->isInHistory()) {
                $validator->errors()->add('in_history', 'in_history');
            }

            if ($this->post->isScheduleProcessing()) {
                $validator->errors()->add('publishing', 'publishing');
            }
        });
    }

    public function handle()
    {
        return DB::transaction(function () {
            if (empty($this->input('accounts')) || !$this->scheduledAt()) {
                $this->post->setDraft();
            }

            $this->post->accounts()->sync($this->input('accounts'));
            $this->post->tags()->sync($this->input('tags'));

            $this->post->versions()->delete();
            $this->post->versions()->createMany($this->input('versions'));

            return $this->post->update([
                'scheduled_at' => $this->scheduledAt() ? Util::convertTimeToUTC($this->scheduledAt()) : null,
            ]);
        });
    }
}
