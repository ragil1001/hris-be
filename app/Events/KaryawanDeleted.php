<?php

namespace App\Events;

use App\Models\Karyawan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KaryawanDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $karyawan;
    public $actorId;

    public function __construct(Karyawan $karyawan, $actorId)
    {
        $this->karyawan = $karyawan;
        $this->actorId = $actorId;
    }
}
