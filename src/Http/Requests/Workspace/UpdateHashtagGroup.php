<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\HashtagGroup;

class UpdateHashtagGroup extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:3000']
        ];
    }

    public function handle(): int
    {
        $record = HashtagGroup::firstOrFailByUuid($this->route('hashtaggroup'));

        return $record->update([
            'name' => $this->input('name'),
            'content' => $this->input('content'),
        ]);
    }
}
