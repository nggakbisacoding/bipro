<?php

namespace Modules\Auth\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Auth\Entities\User;

class UserResource extends ResourceCollection
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
            'data' => $this->collection->map(function(User $user) {
                return [
                    'id' => $user->id,
                    'type' => $user->type,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->isVerified()
                        ? timezone()->convertToLocal($user->email_verified_at) 
                        : 'Not verified',
                    'two_factor_auth_count' => $user->hasTwoFactorEnabled()
                        ? timezone()->convertToLocal($user->twoFactorAuth->enabled_at)
                        : 'Not enabled',
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'permissions' => $user->permissions_label,
                    'status' => $user->status,
                    'last_login_at' => !is_null($user->last_login_at) 
                        ? timezone()->convertToLocal($user->last_login_at) 
                        : '',
                    'created_at' => timezone()->convertToLocal($user->created_at),
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
