<?php
namespace Rainsens\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Rbac\Contracts\RoleContract;
use Rainsens\Rbac\Exceptions\InvalidArgumentException;
use Rainsens\Rbac\Exceptions\RoleAlreadyExists;
use Rainsens\Rbac\Exceptions\RoleDoesNotExist;
use Rainsens\Rbac\Facades\Rbac;

/**
 * Class Role
 * @package Rainsens\Rbac\Models
 * @property Collection $permits
 * @property Collection $users
 * @property $guard
 */
class Role extends Model implements RoleContract
{
	protected $guarded = ['id'];
	
	public function getTable()
	{
		return config('permits.tables.roles', parent::getTable());
	}
	
	protected static function boot()
	{
		parent::boot();
		static::deleting(function (Role $model) {
			$model->users()->detach();
			$model->permits()->detach();
		});
	}
	
	public static function create(array $attributes)
	{
		if (! isset($attributes['name']) or ! isset($attributes['slug'])) {
			throw new InvalidArgumentException('Role name and slug both are required.');
		}
		
		$attributes['guard'] = Rbac::guard()->name;
		if (static::where($attributes)->first()) {
			throw new RoleAlreadyExists("Role provided already exists.");
		}
		return static::query()->create($attributes);
	}
	
	public static function findByName(string $name)
	{
		$role = static::where(['name' => $name, 'guard' => Rbac::guard()->name])->first();
		
		if (! $role) {
			throw new RoleDoesNotExist("Role name provided does not exist.");
		}
		return $role;
	}
	
	public static function findById(int $id)
	{
		$role = static::where(['id' => $id, 'guard' => Rbac::guard()->name])->first();
		
		if (! $role) {
			throw new RoleDoesNotExist("Role id provided does not exist.");
		}
		return $role;
	}
	
	public function permits(): BelongsToMany
	{
		return $this->belongsToMany(
			Rbac::authorize()->permitClass,
			Rbac::authorize()->permitRolesTable,
			'role_id',
			'permit_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Rbac::authorize()->userClass,
			Rbac::authorize()->roleMorphName,
			Rbac::authorize()->roleUsersTable,
			Rbac::authorize()->roleMorphId,
			Rbac::authorize()->roleMorphKey
		);
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function givePermits(...$permits)
	{
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits, $this->guard);
		$this->permits()->sync($expectedButValidPermits->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function removePermits(...$permits)
	{
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits, $this->guard);
		$this->permits()->detach($expectedButValidPermits->pluck('id'));
		$this->load('permits');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function giveToUsers(...$users)
	{
		$expectedUsers = Rbac::supplier()->findExpectedModels(Rbac::authorize()->userInstance, $users);
		$expectedButValidUsers = Rbac::guard()->examine($expectedUsers, $this->guard);
		$this->users()->sync($expectedButValidUsers->pluck('id'));
		$this->load('users');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function removeFromUsers(...$users)
	{
		$expectedUsers = Rbac::supplier()->findExpectedModels(Rbac::authorize()->userInstance, $users);
		$expectedButValidUsers = Rbac::guard()->examine($expectedUsers, $this->guard);
		$this->users()->detach($expectedButValidUsers->pluck('id'));
		$this->load('users');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function hasPermits(...$permits)
	{
		$expectedPermits = Rbac::supplier()->findExpectedModels(Rbac::authorize()->permitInstance, $permits);
		$expectedButValidPermits = Rbac::guard()->examine($expectedPermits, $this->guard);
		$valid = $expectedButValidPermits->pluck('id')->toArray();
		$actual = $this->permits->pluck('id')->toArray();
		return count(array_intersect($valid, $actual)) === count($valid);
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function inUsers(...$users)
	{
		$expectedUsers = Rbac::supplier()->findExpectedModels(Rbac::authorize()->userInstance, $users);
		$expectedButValidUsers = Rbac::guard()->examine($expectedUsers, $this->guard);
		$expected = $expectedButValidUsers->pluck('id')->toArray();
		$actual = $this->users->pluck('id')->toArray();
		return count(array_intersect($expected, $actual)) === count($expected);
	}
}
