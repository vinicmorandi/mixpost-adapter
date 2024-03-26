<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\UploadFile;

class UploadFileController
{
    public function __invoke(UploadFile $uploadFile): JsonResponse
    {
        $file = $uploadFile->handle();

        return response()->json($file);
    }
}
