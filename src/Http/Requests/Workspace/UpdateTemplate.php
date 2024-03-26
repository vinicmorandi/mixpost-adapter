<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Template;

class UpdateTemplate extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content.*.body' => ['nullable', 'string'],
            'content.*.media' => ['array'],
            'content.*.media.*' => ['integer'],
        ];
    }

    public function handle(): int
    {
        $record = Template::firstOrFailByUuid($this->route('template'));

        return $record->update([
            'name' => $this->input('name'),
            'content' => $this->input('content'),
        ]);
    }
}
