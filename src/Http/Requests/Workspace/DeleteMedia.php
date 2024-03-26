<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Media;

class DeleteMedia extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array']
        ];
    }

    public function handle()
    {
        foreach ($this->input('items') as $id) {
            $media = Media::find($id);

            if (!$media) {
                continue;
            }

            $media->deleteFiles();
            $media->delete();
        }
    }
}
