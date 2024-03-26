<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Block;
use SaguiAi\MixpostAdapter\Rules\ResourceStatusRule;

class UpdateBlock extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'module' => ['required'],
            'content' => ['required', 'array'],
            'status' => ['required', 'integer', new ResourceStatusRule()],
        ];
    }

    public function handle(): bool
    {
        $block = Block::where('id', $this->route('block'))->firstOrFail();

        return $block->update([
            'name' => $this->input('name'),
            'module' => $this->input('module'),
            'content' => $this->input('content'),
            'status' => $this->input('status'),
        ]);
    }
}
