<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\PostingSchedule;

class UpdatePostingSchedule extends FormRequest
{
    public function rules(): array
    {
        return [
            'times' => ['required', 'array', 'size:7'],
            'times.*.id' => ['required', 'integer'],
            'times.*.status' => ['required', 'boolean'],
            'times.*.times' => ['array'],
            'times.*.times.*.value' => ['required', 'date_format:H:i']
        ];
    }

    public function handle(): void
    {
        $record = PostingSchedule::first();

        if (!$record) {
            PostingSchedule::create([
                'times' => $this->input('times')
            ]);

            return;
        }

        $record->update([
            'times' => $this->input('times')
        ]);
    }
}
