<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Variable;

class StoreVariable extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:3000']
        ];
    }

    public function handle()
    {
        return Variable::create([
            'name' => $this->input('name'),
            'value' => $this->input('value'),
        ]);
    }
}
