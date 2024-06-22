<?php

namespace Modules\Auth\Events\Role;

use Modules\Auth\Entities\Role;
use Illuminate\Queue\SerializesModels;

/**
 * Class RoleUpdated.
 */
class RoleUpdated
{
    use SerializesModels;

    /**
     * @var
     */
    public $role;

    /**
     * @param $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }
}
