<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectDeleted
{
    use Dispatchable, SerializesModels;

    public $project;
    public $actorId;

    public function __construct(Project $project, $actorId)
    {
        $this->project = $project;
        $this->actorId = $actorId;
    }
}
