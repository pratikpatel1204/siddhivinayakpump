<?php
namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\BouncerFacade as Bouncer;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (!$request->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
