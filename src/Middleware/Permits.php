<?php
namespace Rainsens\Rbac\Middleware;

use Closure;
use Rainsens\Rbac\Facades\Rbac;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Exceptions\UnauthorizedException;

class Permits
{
	public function handle($request, Closure $next)
	{
		$guardName = Rbac::guard()->name;
		$user = Auth::guard($guardName)->user();
		
		// If not logged in
		if (Auth::guard($guardName)->guest()) {
			throw new UnauthorizedException('Has not logged in.');
		}
		
		// If can pass url
		if (! $user->hasPathPermit()) {
			throw new UnauthorizedException('You are not allowed to access.');
		}
		
		return $next($request);
	}
}
