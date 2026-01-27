<?php

namespace App\Events;

use App\Models\Jabatan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JabatanDeleted
{
    use Dispatchable, SerializesModels;

    public $jabatan;
    public $actorId;

    public function __construct(Jabatan $jabatan, $actorId)
    {
        $this->jabatan = $jabatan;
        $this->actorId = $actorId;
    }
}
