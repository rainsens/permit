<?php
namespace Rainsens\Authorize\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
	public function roles(): BelongsToMany
	{
		return $this->morphToMany(config('permits.models.role'), 'rolable');
	}
	
	public function assignRole(...$roles)
	{
		$roles = collect($roles)
			->flatten()
			->map(function ($role) {
				if (empty($role)) {
					return false;
				}
				
				return $this->getRoleModel($role);
			})
			->filter(function ($role) {
				return $role instanceof Role;
			})
			->map->id
			->all();
		
		$model = $this->getModel();
		
		$this->roles()->sync($roles, false);
		$model->load('roles');
		
		return $this;
	}
	
	public function removeRole($role)
	{
		$this->detach($this->getRoleModel($role));
		$this->load('roles');
		return $this;
	}
	
	public function syncRoles(...$roles)
	{
		$this->roles()->detach();
		return $this->assignRole($roles);
	}
	
	
	public function hasRole($roles, string $guard = null): bool
	{
		if (is_string($roles) && false !== strpos($roles, '|')) {
			$roles = $this->convertPipeToArray($roles);
		}
		
		if (is_string($roles)) {
			return $guard
				? $this->roles->where('guard_name', $guard)->contains('name', $roles)
				: $this->roles->contains('name', $roles);
		}
		
		if (is_string($roles)) {
			return $guard
				? $this->roles->where('guard_name', $guard)->contains('id', $roles)
				: $this->roles->contains('id', $roles);
		}
		
		if ($roles instanceof Role) {
			return $this->roles->contains('id', $roles->id);
		}
		
		if (is_array($roles)) {
			foreach ($roles as $role) {
				if ($this->hasRole($role, $guard)) {
					return true;
				}
			}
			return false;
		}
		
		return $roles->intersect($guard ? $this->roles->where('guard_name', $guard) : $this->roles)->isNotEmpty();
	}
	
	public function hasAnyRole(...$roles)
	{
		return $this->hasRole($roles);
	}
	
	public function hasAllRoles($roles, string $guard = null): bool
	{
		if (is_string($roles) && false !== strpos($roles, '|')) {
			$roles = $this->convertPipeToArray($roles);
		}
		
		if (is_string($roles)) {
			return $guard
				? $this->roles->where('guard_name', $guard)->contains('name', $roles)
				: $this->roles->contains('name', $roles);
		}
		
		if ($roles instanceof RoleContract) {
			return $this->roles->contains('id', $roles->id);
		}
		
		$roles = collect()->make($roles)->map(function ($role) {
			return $role instanceof RoleContract ? $role->name : $role;
		});
		
		return $roles->intersect(
			$guard
				? $this->roles->where('guard_name', $guard)->pluck('name')
				: $this->getRoleNames()
		) === $roles;
	}
	
	protected function getRoleNames(): Collection
	{
		return $this->roles->pluck('name');
	}
	
	protected function getRoleModel($role)
	{
		$roleClass = app(config('permits.models.role'));
		
		if (is_numeric($role)) {
			return $roleClass->findById($role, Guard::getDefaultName($this));
		}
		
		if (is_string($role)) {
			return $roleClass->findByName($role, Guard::getDefaultName($this));
		}
		
		return $role;
	}
	
	protected function convertPipeToArray(string $pipeString)
	{
		$pipeString = trim($pipeString);
		
		if (strlen($pipeString) <= 2) {
			return $pipeString;
		}
		
		$quoteCharacter = substr($pipeString, 0, 1);
		$endCharacter = substr($quoteCharacter, -1, 1);
		
		if ($quoteCharacter !== $endCharacter) {
			return explode('|', $pipeString);
		}
		
		if (! in_array($quoteCharacter, ["'", '"'])) {
			return explode('|', $pipeString);
		}
		
		return explode('|', trim($pipeString, $quoteCharacter));
	}
}
