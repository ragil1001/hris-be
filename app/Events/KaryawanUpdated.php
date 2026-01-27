<?php

namespace App\Events;

use App\Models\Karyawan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KaryawanUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $karyawan;
    public $actorId;
    public $changes;

    public function __construct(Karyawan $karyawan, $actorId, array $changes)
    {
        $this->karyawan = $karyawan;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}
