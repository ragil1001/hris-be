<?php

namespace App\Listeners;

use App\Events\IzinCreated;
use App\Events\IzinUpdated;
use App\Events\IzinDeleted;
use App\Jobs\LogAuditJob;

class LogIzinActivity
{
    public function handle($event)
    {
        $izin = $event->izin;
        $actorId = $event->actorId;
        $eventType = match (true) {
            $event instanceof IzinCreated => 'izin_created',
            $event instanceof IzinUpdated => 'izin_updated',
            $event instanceof IzinDeleted => 'izin_deleted',
        };
        $meta = $event instanceof IzinUpdated ? $event->changes : ['izin_id' => $izin->id];

        LogAuditJob::dispatch($actorId, $eventType, request()->ip(), request()->userAgent(), $meta);
    }
}
