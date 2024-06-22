<?php

namespace Modules\Auth\Http\Middleware;

use Modules\Auth\Entities\User;
use Closure;

/**
 * Class UserCheck.
 */
class UserCheck
{
    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isType(User::TYPE_USER)) {
            return $next($request);
        }

        return redirect()->route('frontend.index')->withFlashDanger(__('You do not have access to do that.'));
    }
}
