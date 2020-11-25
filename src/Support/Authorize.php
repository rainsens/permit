<?php
namespace Rainsens\Rbac\Support;

use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Contracts\RoleContract;
use Rainsens\Rbac\Exceptions\InvalidArgumentException;
use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Models\Role;

/**
 * Class Authorize
 * @package Rainsens\Rbac\Support
 * @property $permitClass
 * @property $roleClass
 * @property $userClass
 * @property $permitInstance
 * @property $roleInstance
 * @property $userInstance
 * @property $permitsTable
 * @property $rolesTable
 * @property $permitRolesTable
 * @property $permitUsersTable
 * @property $roleUsersTable
 */
class Authorize
{
	protected $permitClass;
	protected $roleClass;
	protected $userClass;
	
	protected $permitInstance;
	protected $roleInstance;
	protected $userInstance;
	
	protected $permitsTable;
	protected $rolesTable;
	protected $permitRolesTable;
	protected $permitUsersTable;
	protected $roleUsersTable;
	
	public function __construct()
	{
		$this->permitsTable = $this->getTable('permits', 'permits');
		$this->rolesTable = $this->getTable('roles', 'roles');
		$this->permitRolesTable = $this->getTable('permit_roles', 'permit_roles');
		$this->permitUsersTable = $this->getTable('permit_users', 'permit_users');
		$this->roleUsersTable = $this->getTable('role_users', 'role_users');
		
		$this->permitClass = $this->permitClass();
		$this->roleClass = $this->roleClass();
		$this->userClass = $this->guard()->providerClass;
		
		$this->permitInstance = app($this->permitClass);
		$this->roleInstance = app($this->roleClass);
		$this->userInstance = app($this->userClass);
	}
	
	protected function getTable(string $name, string $default)
	{
		return config("rbac.table_names.{$name}", $default);
	}
	
	protected function permitClass()
	{
		$class = config('rbac.models.permit', Permit::class);
		
		if (! app($class) instanceof PermitContract) {
			throw new InvalidArgumentException('Permit class name given is not valid.');
		}
		
		return $class;
	}
	
	protected function roleClass()
	{
		$class = config('rbac.models.role', Role::class);
		
		if (! app($class) instanceof RoleContract) {
			throw new InvalidArgumentException('Role class name given is not valid.');
		}
		
		return $class;
	}
	
	public function getPermitOrRoleModels($instance, ...$params)
	{
		$guareName = $this->guard()->name;
		
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
	
	public function guard(): Guard
	{
		return app(Guard::class);
	}
	
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}
