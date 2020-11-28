<?php
namespace Rainsens\Rbac;

use Rainsens\Rbac\Support\Guard;
use Rainsens\Rbac\Support\Authorize;
use Rainsens\Rbac\Support\Supplier;

class Rbac
{
	public function authorize(): Authorize
	{
		return app(Authorize::class);
	}
	
	public function guard(): Guard
	{
		return app(Guard::class);
	}
	
	public function supplier(): Supplier
	{
		return app(Supplier::class);
	}
}
