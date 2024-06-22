<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Modules\Auth\Entities\User;

/**
 * Class AdminCheck.
 */
class AdminCheck
{
    /**
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isType(User::TYPE_ADMIN)) {
            return $next($request);
        }

        return redirect()
            ->route('frontend.user.insight.index')
            ->withFlashDanger(__('You do not have access to do that.'));
    }
}
