<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Variable;

class UpdateVariable extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:3000']
        ];
    }

    public function handle(): int
    {
        $record = Variable::firstOrFailByUuid($this->route('variable'));

        return $record->update([
            'name' => $this->input('name'),
            'value' => $this->input('value'),
        ]);
    }
}
