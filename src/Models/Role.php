<?php
namespace Rainsens\Authorize\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rainsens\Authorize\Contracts\RoleContract;
use Rainsens\Authorize\Exceptions\RoleAlreadyExists;
use Rainsens\Authorize\Exceptions\RoleDoesNotExist;
use Rainsens\Authorize\Facades\Authorize;
use Rainsens\Authorize\Traits\HasPermits;

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
		$attributes['guard'] = Authorize::guardName();
		
		if (static::where($attributes)->first()) {
			throw new RoleAlreadyExists("Role name provided already exists.");
		}
		
		return static::query()->create($attributes);
	}
	
	public static function findByName(string $name)
	{
		$role = static::where(['name' => $name, 'guard' => Authorize::guardName()])->first();
		
		if (! $role) {
			throw new RoleDoesNotExist("Role name provided does not exist.");
		}
		
		return $role;
	}
	
	public static function findById(int $id)
	{
		$role = static::where(['id' => $id, 'guard' => Authorize::guardName()])->first();
		
		if (! $role) {
			throw new RoleDoesNotExist("Role id provided does not exist.");
		}
		
		return $role;
	}
	
	public function permitItems(): BelongsToMany
	{
		return $this->belongsToMany(
			Authorize::permitClass(), Authorize::permitRolesTable(), 'role_id', 'permit_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Authorize::authClass(), 'rolable'
		);
	}
	
	public function giveRolePermits(...$permits)
	{
		$permitModels = Authorize::getPermitOrRoleModels(Authorize::permitInstance(), $permits);
		$this->permitItems()->sync($permitModels->pluck('id'));
		$this->load('permitItems');
		return $this;
	}
	
	public function removeRolePermits(...$permits)
	{
		$permitModels = Authorize::getPermitOrRoleModels(Authorize::permitInstance(), $permits);
		$this->permitItems()->detach($permitModels->pluck('id'));
		$this->load('permitItems');
		return $this;
	}
	
	public function hasPermitItem($permit)
	{
		$permitModel = (Authorize::getPermitOrRoleModels(Authorize::permitInstance(), $permit))[0];
		return $this->permitItems->containsStrict('id', $permitModel->id);
	}
}
