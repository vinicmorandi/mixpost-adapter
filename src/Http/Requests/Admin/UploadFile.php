<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use SaguiAi\MixpostAdapter\Support\MediaUploader;

class UploadFile extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types($this->allowedTypes())]
        ];
    }

    private function allowedTypes(): string
    {
        return collect([
            'image/jpg',
            'image/jpeg',
            'image/gif',
            'image/png',
        ])->map(function ($mime) {
            return Str::after($mime, '/');
        })->implode(',');
    }

    public function handle(): array
    {
        $date = Carbon::now()->format('m-Y');

        return MediaUploader::fromFile($this->file('file'))
            ->path("uploads/$date")
            ->upload();
    }

    public function messages(): array
    {
        return [
            'file.required' => __('mixpost::rules.file_required')
        ];
    }
}
