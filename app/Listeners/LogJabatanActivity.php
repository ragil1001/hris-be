<?php

namespace App\Listeners;

use App\Events\JabatanCreated;
use App\Events\JabatanUpdated;
use App\Events\JabatanDeleted;
use App\Jobs\LogAuditJob;

class LogJabatanActivity
{
    public function handle($event)
    {
        $jabatan = $event->jabatan;
        $actorId = $event->actorId;
        $eventType = match (true) {
            $event instanceof JabatanCreated => 'jabatan_created',
            $event instanceof JabatanUpdated => 'jabatan_updated',
            $event instanceof JabatanDeleted => 'jabatan_deleted',
        };
        $meta = $event instanceof JabatanUpdated ? $event->changes : ['jabatan_id' => $jabatan->id];

        LogAuditJob::dispatch($actorId, $eventType, request()->ip(), request()->userAgent(), $meta);
    }
}
