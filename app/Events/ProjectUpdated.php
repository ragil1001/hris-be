<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectUpdated
{
    use Dispatchable, SerializesModels;

    public $project;
    public $actorId;
    public $changes;

    public function __construct(Project $project, $actorId, array $changes)
    {
        $this->project = $project;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}
