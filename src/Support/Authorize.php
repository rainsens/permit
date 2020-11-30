<?php
namespace Rainsens\Rbac\Support;

use Rainsens\Rbac\Models\Role;
use Rainsens\Rbac\Facades\Rbac;
use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Contracts\RoleContract;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Exceptions\InvalidArgumentException;

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
 * @property $permitMorphId
 * @property $permitMorphName
 * @property $permitMorphKey
 * @property $permitMorphType
 * @property $roleMorphId
 * @property $roleMorphName
 * @property $roleMorphKey
 * @property $roleMorphType
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

	protected $permitMorphId;
	protected $permitMorphName;
	protected $permitMorphKey;
	protected $permitMorphType;

	protected $roleMorphId;
	protected $roleMorphName;
	protected $roleMorphKey;
	protected $roleMorphType;
	
	public function __construct()
	{
		$this->permitsTable = $this->getTable('permits', 'permits');
		$this->rolesTable = $this->getTable('roles', 'roles');
		$this->permitRolesTable = $this->getTable('permit_roles', 'permit_roles');
		$this->permitUsersTable = $this->getTable('permit_users', 'permit_users');
		$this->roleUsersTable = $this->getTable('role_users', 'role_users');
		
		$this->permitMorphId = $this->getColumn('permit_morph_id', 'permit_id');
		$this->permitMorphName = $this->getColumn('permit_morph_name', 'permitable');
		$this->permitMorphKey = $this->getColumn('permit_morph_key', 'permitable_id');
		$this->permitMorphType = $this->getColumn('permit_morph_type', 'permitable_type');
		
		$this->roleMorphId = $this->getColumn('role_morph_id', 'role_id');
		$this->roleMorphName = $this->getColumn('role_morph_name', 'rolable');
		$this->roleMorphKey = $this->getColumn('role_morph_key', 'rolable_id');
		$this->roleMorphType = $this->getColumn('role_morph_type', 'rolable_type');
		
		$this->permitClass = $this->permitClass();
		$this->roleClass = $this->roleClass();
		$this->userClass = Rbac::guard()->providerClass;
		
		$this->permitInstance = app($this->permitClass);
		$this->roleInstance = app($this->roleClass);
		$this->userInstance = app($this->userClass);
	}
	
	protected function getTable(string $name, string $default)
	{
		return config("rbac.tables.{$name}", $default);
	}
	
	protected function getColumn(string $name, string $default)
	{
		return config("rbac.columns.{$name}", $default);
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
	
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}
