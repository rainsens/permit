<?php
namespace Rainsens\Rbac\Traits;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Str;
use Rainsens\Rbac\Facades\Rbac;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rainsens\Rbac\Models\Permit;

/**
 * Trait RbacTrait
 * @package Rainsens\Rbac\Traits
 * @property Collection $permits
 * @property Collection $allPermits
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
	
	public function allPermits(): Collection
	{
		return $this->roles()->with('permits')->get()
			->pluck('permits')->flatten()
			->merge($this->permits);
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
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function givePermits(...$permits)
	{
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits);
		$this->permits()->sync($expectedButValidPermits);
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function removePermits(...$permits)
	{
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits);
		$this->permits()->detach($expectedButValidPermits->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function giveRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$expectedButValidRoles = Rbac::guard()->examine($expectedRoles);
		$this->roles()->sync($expectedButValidRoles);
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function removeRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$expectedButValidRoles = Rbac::guard()->examine($expectedRoles);
		$this->roles()->detach($expectedButValidRoles->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function hasRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$expectedButValidRoles = Rbac::guard()->examine($expectedRoles);
		$valid = $expectedButValidRoles->pluck('id')->toArray();
		$actual = $this->roles->pluck('id')->toArray();
		return count(array_intersect($valid, $actual)) === count($valid);
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function hasPermits(...$permits)
	{
		dd($permits);
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits);
		$valid = $expectedButValidPermits->pluck('id')->toArray();
		$actual = $this->allPermits()->pluck('id')->toArray();
		return count(array_intersect($valid, $actual)) === count($valid);
	}
	
	public function hasPathPermit()
	{
		$args = Rbac::supplier()->pathArgs();
		
		// If path has not definded, all users are allowed to access.
		if (! Permit::all()->pluck('path')->flatten()->contains($args['path'])) {
			return true;
		}
		
		$methodAndPath = $path = false;
		foreach (Rbac::guard()->examine($this->allPermits()) as $permit) {
			if ($permit->method) {
				// Target in method and path array directly
				if (in_array($args['method'], $permit->method) and in_array($args['path'], $permit->path)) {
					$methodAndPath = true;
				}
				// Target path with wildcard
				$methodAndPath = $permit->path->map(function ($item) use ($args) {
					if (Str::endsWith($item, '*')) {
						return in_array($args['method'], $item->method) and Str::contains($item, $args['path']);
					}
					return false;
				});
			} else {
				// Target path in path array directly.
				if (in_array($args['path'], $permit->path)) {
					$path = true;
				}
				// Target path with wildcard
				$path = $permit->path->map(function ($item) use ($args) {
					if (Str::endsWith($item, '*')) {
						return Str::contains($item, $args['path']);
					}
					return false;
				});
			}
		}
		
		return $methodAndPath || $path;
	}
	
	public function isSuper(): bool
	{
		return $this->hasRoles('adm');
	}
}
