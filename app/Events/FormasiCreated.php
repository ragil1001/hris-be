<?php

namespace App\Events;

use App\Models\Formasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormasiCreated
{
    use Dispatchable, SerializesModels;

    public $formasi;
    public $actorId;

    public function __construct(Formasi $formasi, $actorId)
    {
        $this->formasi = $formasi;
        $this->actorId = $actorId;
    }
}
