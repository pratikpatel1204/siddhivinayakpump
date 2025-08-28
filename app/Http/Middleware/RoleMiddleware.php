<?php
namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\BouncerFacade as Bouncer;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Bouncer::is($request->user())->a($role)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
