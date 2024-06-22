<?php

namespace Modules\Project\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Project\Entities\Project;

class ProjectDeleted
{
    use SerializesModels;

    public $afterCommit = true;

    public Project $project;

    /**
     * Constructs a new instance of the class.
     *
     * @param  Project  $project The project object.
     */
    public function __construct(
        Project $project
    ) {
        $this->project = $project;
    }
}
