<?php

namespace App\Listeners;

use App\Events\FormasiCreated;
use App\Events\FormasiUpdated;
use App\Events\FormasiDeleted;
use App\Jobs\LogAuditJob;

class LogFormasiActivity
{
    public function handle($event)
    {
        $formasi = $event->formasi;
        $actorId = $event->actorId;
        $eventType = match (true) {
            $event instanceof FormasiCreated => 'formasi_created',
            $event instanceof FormasiUpdated => 'formasi_updated',
            $event instanceof FormasiDeleted => 'formasi_deleted',
        };
        $meta = $event instanceof FormasiUpdated ? $event->changes : ['formasi_id' => $formasi->id];

        LogAuditJob::dispatch($actorId, $eventType, request()->ip(), request()->userAgent(), $meta);
    }
}
