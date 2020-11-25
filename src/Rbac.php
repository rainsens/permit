<?php
namespace Rainsens\Rbac;

use Rainsens\Rbac\Support\Guard;
use Rainsens\Rbac\Support\Authorize;

class Rbac
{
	public function guard(): Guard
	{
		return app(Guard::class);
	}
	
	public function authorize(): Authorize
	{
		return app(Authorize::class);
	}
}
