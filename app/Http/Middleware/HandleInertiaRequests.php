<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Modules\Project\Entities\Project;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => function () use ($request) {
                    $user = $request->user();

                    if (is_null($user)) {
                        return [];
                    }

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'roles' => $user->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name,
                            ];
                        }),

                        'projects' => $user->projects->map(function (Project $project) {
                            return [
                                'id' => $project->hashId,
                                'name' => $project->name,
                            ];
                        }),
                    ];
                },
                'activeProjectId' => getActiveProjectId(),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'query' => $request->query(),
            'notification' => [
                'error' => fn () => $request->session()->get('flash_error') ?? $request->session()->get('flash_danger'),
                'success' => fn () => $request->session()->get('flash_success'),
            ],
        ]);
    }
}
