<?php

namespace Modules\Auth\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\User;

class RoleResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function(Role $role) {
                return [
                    'id' => $role->id,
                    'type' => $role->type === User::TYPE_ADMIN ? 'Administrator' : 'User',
                    'name' => $role->name,
                    'permissions' => $role->permissions_label,
                    'users_count' => $role->users()->count()
                ];
            }),
            'pagination' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage()
            ],
        ];
    }

    public function withResponse($request, $response)
    {
        $originalContent = json_decode($response->getContent(), true);
        unset($originalContent['links'],$originalContent['meta']);
        $response->setData($originalContent);
    }
}
