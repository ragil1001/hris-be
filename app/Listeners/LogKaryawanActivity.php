<?php

namespace App\Listeners;

use App\Events\KaryawanCreated;
use App\Events\KaryawanUpdated;
use App\Events\KaryawanDeleted;
use App\Jobs\LogAuditJob;

class LogKaryawanActivity
{
    public function handle($event)
    {
        $karyawan = $event->karyawan;
        $actorId = $event->actorId;
        $eventType = match (true) {
            $event instanceof KaryawanCreated => 'karyawan_created',
            $event instanceof KaryawanUpdated => 'karyawan_updated',
            $event instanceof KaryawanDeleted => 'karyawan_deleted',
        };
        $meta = $event instanceof KaryawanUpdated ? $event->changes : ['karyawan_id' => $karyawan->id];

        LogAuditJob::dispatch($actorId, $eventType, request()->ip(), request()->userAgent(), $meta);
    }
}
