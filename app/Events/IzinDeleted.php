<?php

namespace App\Events;

use App\Models\Izin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IzinDeleted
{
    use Dispatchable, SerializesModels;

    public $izin;
    public $actorId;

    public function __construct(Izin $izin, $actorId)
    {
        $this->izin = $izin;
        $this->actorId = $actorId;
    }
}
