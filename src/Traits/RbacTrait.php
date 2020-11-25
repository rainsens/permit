<?php
namespace Rainsens\Rbac\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Rbac\Facades\Rbac;

/**
 * Trait RbacTrait
 * @package Rainsens\Rbac\Traits
 * @property Collection $permits
 * @property Collection $roles
 */
trait RbacTrait
{
	public function permits(): BelongsToMany
	{
		return $this->morphToMany(
			Rbac::authorize()->permitClass,
			'permitable',
			Rbac::authorize()->permitUsersTable,
			'permitable_id',
			'permit_id'
		);
	}
	
	public function roles(): BelongsToMany
	{
		return $this->morphToMany(
			Rbac::authorize()->roleClass,
			'rolable',
			Rbac::authorize()->roleUsersTable,
			'rolable_id',
			'role_id'
		);
	}
	
	public function givePermits(...$permits)
	{
		$permitModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permits);
		$this->permits()->sync($permitModels->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	public function removePermits(...$permits)
	{
		$permitModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permits);
		$this->permits()->detach($permitModels->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	public function giveRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roles()->sync($roleModels->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	public function removeRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roles()->detach($roleModels->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	public function hasPermit($permit)
	{
		$permitModel = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permit)->first();
		return $this->permits->containsStrict('id', $permitModel->id);
	}
	
	public function hasRole($role)
	{
		$roleModel = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $role)->first();
		return $this->roles->containsStrict('id', $roleModel->id);
	}
}
