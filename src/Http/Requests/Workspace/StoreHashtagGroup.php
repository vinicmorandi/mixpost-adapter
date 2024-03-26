<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\HashtagGroup;

class StoreHashtagGroup extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:3000']
        ];
    }

    public function handle()
    {
        return HashtagGroup::create([
            'name' => $this->input('name'),
            'content' => $this->input('content'),
        ]);
    }
}
