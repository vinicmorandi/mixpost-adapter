<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Support\SystemLogs;

class DownloadSystemLog extends FormRequest
{

    public function rules(): array
    {
        return [
            'filename' => ['required', 'string', 'max:255'],
        ];
    }

    public function handle(): string
    {
        $systemLogs = new SystemLogs();

        $filePath = $systemLogs->getFilePath($this->input('filename'));

        if (!file_exists($filePath)) {
            abort(404);
        }

        return $filePath;
    }
}
