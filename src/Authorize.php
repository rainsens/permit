<?php
namespace Rainsens\Authorize;

use Illuminate\Database\Eloquent\Builder;
use Rainsens\Authorize\Contracts\PermitContract;
use Rainsens\Authorize\Contracts\RoleContract;
use Rainsens\Authorize\Exceptions\GuardDoesNotExist;
use Rainsens\Authorize\Exceptions\InvalidArgumentException;
use Rainsens\Authorize\Models\Permit;
use Rainsens\Authorize\Models\Role;

class Authorize
{
	/**
	 * Get guard name which not only has 'provider', but also has 'model'
	 */
	public function guard()
	{
		$guardName = config('permits.guard') ?? $this->defaultGuardName();
		$guardProviderName = config("auth.guards.{$guardName}.provider");
		$guardProviderModel = config("auth.providers.{$guardProviderName}.model");
		
		if (! isset($guardProviderName) || ! isset($guardProviderModel)) {
			throw new GuardDoesNotExist('Guard name provided is not existing!');
		}
		
		return (object)[
			'guard_name' => $guardName,
			'guard_provider' => $guardProviderName,
			'guard_model' => $guardProviderModel
		];
	}
	
	public function defaultGuardName(): string
	{
		return config('auth.defaults.guard');
	}
	
	public function guardName(): string
	{
		return $this->guard()->guard_name;
	}
	
	public function authClass(): string
	{
		return $this->guard()->guard_model;
	}
	
	public function permitClass()
	{
		$class = config('permits.models.permit', Permit::class);
		
		if (! app($class) instanceof PermitContract) {
			throw new InvalidArgumentException('Permit class name given is not valid.');
		}
		
		return $class;
	}
	
	public function roleClass()
	{
		$class = config('permits.models.role', Role::class);
		
		if (! app($class) instanceof RoleContract) {
			throw new InvalidArgumentException('Role class name given is not valid.');
		}
		
		return $class;
	}
	
	public function permitInstance()
	{
		return app($this->permitClass());
	}
	
	public function roleInstance()
	{
		return app($this->roleClass());
	}
	
	public function permitsTable()
	{
		return config('authorize.table_names.permits', 'permits');
	}
	
	public function rolesTable()
	{
		return config('authorize.table_names.roles', 'roles');
	}
	
	public function permitRolesTable()
	{
		return config('authorize.table_names.permit_roles', 'permit_roles');
	}
	
	public function getPermitOrRoleModels($instance, ...$params)
	{
		$guareName = $this->guardName();
		
		$params = collect($params)
			->flatten()
			->map(function ($value) {
				if (is_object($value)) {
					return $value->id;
				} else {
					return $value;
				}
			});
		
		return $instance
			->where('guard', $guareName)
			->whereIn('id', $params->toArray())
			->orWhereIn('name', $params->toArray())
			->get();
	}
}
