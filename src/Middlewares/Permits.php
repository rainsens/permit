<?php
namespace Rainsens\Rbac\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Exceptions\UnauthorizedException;
use Rainsens\Rbac\Facades\Rbac;

class Permits
{
	public function handle($request, Closure $next, $permits = null, $guard = null)
	{
		$guard = $guard ?? Rbac::guard()->name;
		
		if (Auth::guard($guard)->guest()) {
			throw new UnauthorizedException('Has not logged in.');
		}
		
		$permits = is_array($permits) ? $permits : explode('|', $permits);
		
		if (! Auth::guard($guard)->user()->hasPermits($permits)
			&& Auth::guard($guard)->user()->has) {
			throw new UnauthorizedException('You are not allowed to access.');
		}
		
		return $next($request);
	}
}
