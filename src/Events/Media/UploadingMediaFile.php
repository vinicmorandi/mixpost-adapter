<?php

namespace SaguiAi\MixpostAdapter\Events\Media;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Workspace;

class UploadingMediaFile
{
    use Dispatchable, SerializesModels;

    public ?Workspace $workspace;
    public UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->workspace = WorkspaceManager::current();
        $this->file = $file;
    }
}
