<?php

namespace App\Events;

use App\Models\Izin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IzinUpdated
{
    use Dispatchable, SerializesModels;

    public $izin;
    public $actorId;
    public $changes;

    public function __construct(Izin $izin, $actorId, array $changes)
    {
        $this->izin = $izin;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}
