<?php

namespace Modules\Auth\Http\Controllers\Backend\User;

use Modules\Auth\Http\Requests\Backend\User\ClearUserSessionRequest;
use Modules\Auth\Entities\User;

/**
 * Class UserSessionController.
 */
class UserSessionController
{
    /**
     * @param  ClearUserSessionRequest  $request
     * @param  User  $user
     * @return mixed
     */
    public function update(ClearUserSessionRequest $request, User $user)
    {
        $user->update(['to_be_logged_out' => true]);

        return redirect()->back()->withFlashSuccess(__('The user\'s session was successfully cleared.'));
    }
}
