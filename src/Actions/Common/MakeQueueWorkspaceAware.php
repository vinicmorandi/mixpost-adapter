<?php

namespace SaguiAi\MixpostAdapter\Actions\Common;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Exceptions\CurrentWorkspaceCouldNotBeDeterminedInWorkspaceAwareJob;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Workspace;

class MakeQueueWorkspaceAware
{
    public function __invoke(): void
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed()
            ->listenForJobsRetryRequested();
    }

    protected function listenForJobsBeingQueued(): static
    {
        app('queue')->createPayloadUsing(function ($connectionName, $queue, $payload) {
            $queueable = $payload['data']['command'];

            if (!$this->isWorkspaceAware($queueable)) {
                return [];
            }

            return ['mixpost_workspace_id' => WorkspaceManager::current()?->id];
        });

        return $this;
    }

    protected function listenForJobsBeingProcessed(): static
    {
        app('events')->listen(JobProcessing::class, function (JobProcessing $event) {
            if (array_key_exists('mixpost_workspace_id', $event->job->payload())) {
                WorkspaceManager::setCurrent($this->findWorkspace($event));
            }
        });

        return $this;
    }

    protected function listenForJobsRetryRequested(): static
    {
        app('events')->listen(JobRetryRequested::class, function (JobRetryRequested $event) {
            WorkspaceManager::forgetCurrent();

            if (array_key_exists('mixpost_workspace_id', $event->payload())) {
                WorkspaceManager::setCurrent($this->findWorkspace($event));
            }
        });

        return $this;
    }

    protected function isWorkspaceAware(object $queueable): bool
    {
        $reflection = new \ReflectionClass($this->getJobFromQueueable($queueable));

        if ($reflection->implementsInterface(QueueWorkspaceAware::class)) {
            return true;
        }

        return false;
    }

    protected function getEventPayload($event): ?array
    {
        return match (true) {
            $event instanceof JobProcessing => $event->job->payload(),
            $event instanceof JobRetryRequested => $event->payload(),
            default => null,
        };
    }

    protected function findWorkspace(JobProcessing|JobRetryRequested $event): Workspace
    {
        $workspaceId = $this->getEventPayload($event)['mixpost_workspace_id'] ?? null;

        if (!$workspaceId) {
            $event->job->delete();

            throw CurrentWorkspaceCouldNotBeDeterminedInWorkspaceAwareJob::noIdSet($event);
        }


        if (!$workspace = Workspace::find($workspaceId)) {
            $event->job->delete();

            throw CurrentWorkspaceCouldNotBeDeterminedInWorkspaceAwareJob::noTenantFound($event);
        }

        return $workspace;
    }

    protected function getJobFromQueueable(object $queueable)
    {
        $queueableToJobs = [
            SendQueuedMailable::class => 'mailable',
            SendQueuedNotifications::class => 'notification',
            CallQueuedListener::class => 'class',
            BroadcastEvent::class => 'event',
        ];

        $job = Arr::get($queueableToJobs, $queueable::class);

        if (!$job) {
            return $queueable;
        }

        if (method_exists($queueable, $job)) {
            return $queueable->{$job}();
        }

        return $queueable->$job;
    }
}
