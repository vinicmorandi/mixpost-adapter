<?php

namespace SaguiAi\MixpostAdapter\Exceptions;

use Exception;
use Illuminate\Queue\Events\JobProcessing;

class CurrentWorkspaceCouldNotBeDeterminedInWorkspaceAwareJob extends Exception
{
    public static function noIdSet(JobProcessing $event)
    {
        return new static("The current workspace could not be determined in a job named `" . $event->job->getName() . "`. No `workspace_id` was set in the payload.");
    }

    public static function noTenantFound(JobProcessing $event): self
    {
        return new static("The current workspace could not be determined in a job named `" . $event->job->getName() . "`. The workspace manager could not find a workspace.");
    }
}
