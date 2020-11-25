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
			Rbac::authorize()->permitMorphName,
			Rbac::authorize()->permitUsersTable,
			Rbac::authorize()->permitMorphKey,
			Rbac::authorize()->permitMorphId
		);
	}
	
	public function roles(): BelongsToMany
	{
		return $this->morphToMany(
			Rbac::authorize()->roleClass,
			Rbac::authorize()->roleMorphName,
			Rbac::authorize()->roleUsersTable,
			Rbac::authorize()->roleMorphKey,
			Rbac::authorize()->roleMorphId
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
	
	public function hasPermits($permits)
	{
		$permitModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permits);
		foreach ($permitModels as $model) {
			if (! $this->permits->containsStrict('id', $model->id)) {
				return false;
			}
		}
		return true;
	}
	
	public function hasRoles($roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		foreach ($roleModels as $model) {
			if (! $this->roles->containsStrict('id', $model->id)) {
				return false;
			}
		}
		return true;
	}
}
