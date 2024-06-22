<?php

namespace Modules\Auth\Events\User;

use Modules\Auth\Entities\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserDeleted.
 */
class UserDeleted
{
    use SerializesModels;

    /**
     * @var
     */
    public $user;

    /**
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
