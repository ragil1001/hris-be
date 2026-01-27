<?php

namespace App\Events;

use App\Models\Jabatan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JabatanUpdated
{
    use Dispatchable, SerializesModels;

    public $jabatan;
    public $actorId;
    public $changes;

    public function __construct(Jabatan $jabatan, $actorId, array $changes)
    {
        $this->jabatan = $jabatan;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}
