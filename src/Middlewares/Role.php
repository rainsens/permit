<?php
namespace Rainsens\Permit\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Rainsens\Permit\Exceptions\UnauthorizedException;

class Role
{
	public function handle($request, Closure $next, $guard = null)
	{
		if (Auth::guard($guard)->guest()) {
			throw UnauthorizedException::notLoggedIn();
		}
		
		if (! Auth::guard($guard)->user()->hasAnyRole()) {
			throw UnauthorizedException::forRoles();
		}
		
		return $next($request);
	}
}
