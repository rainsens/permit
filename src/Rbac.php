<?php
namespace Rainsens\Rbac;

use Rainsens\Rbac\Support\Guard;
use Rainsens\Rbac\Support\Supplier;
use Rainsens\Rbac\Support\Authorize;
use Illuminate\Database\Eloquent\Model;

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
	
	public function permit(): Model
	{
		return app($this->authorize()->permitClass);
	}
}
