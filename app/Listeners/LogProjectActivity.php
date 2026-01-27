<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use App\Events\ProjectDeleted;
use App\Jobs\LogAuditJob;

class LogProjectActivity
{
    public function handle($event)
    {
        $project = $event->project;
        $actorId = $event->actorId;
        $eventType = match (true) {
            $event instanceof ProjectCreated => 'project_created',
            $event instanceof ProjectUpdated => 'project_updated',
            $event instanceof ProjectDeleted => 'project_deleted',
        };
        $meta = $event instanceof ProjectUpdated ? $event->changes : ['project_id' => $project->id];

        LogAuditJob::dispatch($actorId, $eventType, request()->ip(), request()->userAgent(), $meta);
    }
}
