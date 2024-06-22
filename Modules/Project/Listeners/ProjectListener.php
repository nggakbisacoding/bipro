<?php

namespace Modules\Project\Listeners;

use Illuminate\Events\Dispatcher;
use Modules\Project\Events\ProjectCreated;
use Modules\Project\Events\ProjectDeleted;
use Modules\Project\Events\ProjectUpdated;

class ProjectListener
{
    public function onCreated($event)
    {
        activity('project')
            ->performedOn($event->project)
            ->withProperties([
                'project' => [
                ],
            ])
            ->log(':causer.name created project :subject.name');
    }

    public function onUpdated($event)
    {
        activity('project')
            ->performedOn($event->project)
            ->withProperties([
                'project' => [

                ],
            ])
            ->log(':causer.name updated project :subject.name');
    }

    public function onDeleted($event)
    {
        activity('project')
            ->performedOn($event->project)
            ->log(':causer.name deleted project :subject.name');
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            ProjectCreated::class => 'onCreated',
            ProjectUpdated::class => 'onUpdated',
            ProjectDeleted::class => 'onDeleted',
        ];
    }
}
