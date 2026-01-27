<?php

namespace App\Events;

use App\Models\Formasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormasiUpdated
{
    use Dispatchable, SerializesModels;

    public $formasi;
    public $actorId;
    public $changes;

    public function __construct(Formasi $formasi, $actorId, array $changes)
    {
        $this->formasi = $formasi;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}
