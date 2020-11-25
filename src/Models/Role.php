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
 * @property Collection $permitItems
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
	
	public function permitItems(): BelongsToMany
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
			Rbac::authorize()->userClass, 'rolable'
		);
	}
	
	public function giveRolePermits(...$permits)
	{
		$permitModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permits);
		$this->permitItems()->sync($permitModels->pluck('id'));
		$this->load('permitItems');
		return $this;
	}
	
	public function removeRolePermits(...$permits)
	{
		$permitModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permits);
		$this->permitItems()->detach($permitModels->pluck('id'));
		$this->load('permitItems');
		return $this;
	}
	
	public function hasPermitItem($permit)
	{
		$permitModel = (Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->permitInstance, $permit))[0];
		return $this->permitItems->containsStrict('id', $permitModel->id);
	}
}
