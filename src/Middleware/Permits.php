<?php
namespace Rainsens\Rbac\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Exceptions\UnauthorizedException;

class Permits
{
	public function handle($request, Closure $next, string $guard)
	{
		// If not logged in
		if (Auth::guard($guard)->guest()) {
			throw new UnauthorizedException('Has not logged in.', 403);
		}
		
		// If can pass url
		if (! Auth::guard($guard)->user()->hasPathPermit()) {
			throw new UnauthorizedException('You are not allowed to access.', 403);
		}
		
		return $next($request);
	}
}
