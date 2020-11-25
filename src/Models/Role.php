<?php
namespace Rainsens\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Rbac\Contracts\RoleContract;
use Rainsens\Rbac\Exceptions\RoleAlreadyExists;
use Rainsens\Rbac\Exceptions\RoleDoesNotExist;
use Rainsens\Rbac\Facades\Rbac;

/**
 * Class Role
 * @package Rainsens\Rbac\Models
 * @property Collection $permits
 * @property Collection $users
 */
class Role extends Model implements RoleContract
{
	protected $guarded = ['id'];
	
	public function getTable()
	{
		return config('permits.table_names.roles', parent::getTable());
	}
	
	public static function create(string $roleName)
	{
		$attributes['name'] = $roleName;
		$attributes['guard'] = Rbac::guard()->name;
		
		if (static::where($attributes)->first()) {
			throw new RoleAlreadyExists("Role name provided already exists.");
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
			'role_id', 'permit_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Rbac::authorize()->userClass,
			'rolable',
			Rbac::authorize()->roleUsersTable,
			'role_id',
			'rolable_id'
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
	
	public function giveToUsers(...$users)
	{
		$userModels = Rbac::authorize()->getUserModels($users);
		$this->users()->sync($userModels->pluck('id'));
		$this->load('users');
		return $this;
	}
	
	public function removeFromUsers(...$users)
	{
		$userModels = Rbac::authorize()->getUserModels($users);
		$this->users()->detach($userModels->pluck('id'));
		$this->load('users');
		return $this;
	}
	
	public function hasPermit($permit)
	{
		$permitModel = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permit)->first();
		return $this->permits->containsStrict('id', $permitModel->id);
	}
	
	public function underUser($user)
	{
		$userModel = Rbac::authorize()->getUserModels($user)->first();
		return $this->users->containsStrict('id', $userModel->id);
	}
}
