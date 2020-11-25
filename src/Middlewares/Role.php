<?php
namespace Rainsens\Rbac\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Exceptions\UnauthorizedException;

class Role
{
	public function handle($request, Closure $next, $guard = null)
	{
		if (Auth::guard($guard)->guest()) {
			throw new UnauthorizedException('Has not logged in.');
		}
		
		$role = '/';
		
		if (! Auth::guard($guard)->user()->hasRole($role)) {
			throw new UnauthorizedException('');
		}
		
		return $next($request);
	}
}
