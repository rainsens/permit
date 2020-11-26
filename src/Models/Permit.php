<?php
namespace Rainsens\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Exceptions\InvalidArgumentException;
use Rainsens\Rbac\Exceptions\PermitAlreadyExists;
use Rainsens\Rbac\Exceptions\PermitDoesNotExist;
use Rainsens\Rbac\Facades\Rbac;

/**
 * Class Permit
 * @package Rainsens\Rbac\Models
 * @property Collection $roles
 * @property Collection $users
 */
class Permit extends Model implements PermitContract
{
	protected $guarded = ['id'];
	
	public static $methodMaps = [
		'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'
	];
	
	public function getTable()
	{
		return config('permits.tables.permits', parent::getTable());
	}
	
	public function setMethodAttribute($value)
	{
		$this->attributes['method'] = strtoupper($value);
	}
	
	public function setPathAttribute($value)
	{
		$this->attributes['path'] = str_replace('\/', '/', $value);
	}
	
	public static function create(string $name, string $path = null, string $method = null)
	{
		$attributes['name'] = $name;
		$attributes['guard'] = Rbac::guard()->name;
		
		// Create with route
		if (isset($path)) {
			$attributes['path'] = $path;
			$attributes['method'] = in_array(strtoupper($method), static::$methodMaps) ? $method : null;
		}
		
		if (static::where($attributes)->first()) {
			throw new PermitAlreadyExists("Permit provided already exists.");
		}
		
		return static::query()->create($attributes);
	}
	
	public static function findByName(string $name)
	{
		return static::where(['name' => $name, 'guard' => Rbac::guard()->name])->first();
	}
	
	public static function findById(int $id)
	{
		return static::where(['id' => $id, 'guard' => Rbac::guard()->name])->first();
	}
	
	public static function findByPath(string $path, string $method = null)
	{
		if (! isset($path)) {
			throw new InvalidArgumentException('Permit path provided are not valid.');
		}
		
		$attributes['path'] = $path;
		
		if (isset($method) && in_array(strtoupper($method), static::$methodMaps)) {
			$attributes['method'] = strtoupper($method);
		}
		
		$attributes['guard'] = Rbac::guard()->name;
		
		if (! $permit = static::where($attributes)->first()) {
			throw new PermitDoesNotExist('Permit path provided does not exist.');
		}
		
		return $permit;
	}
	
	public function roles()
	{
		return $this->belongsToMany(
			Rbac::authorize()->roleClass,
			Rbac::authorize()->permitRolesTable,
			'permit_id',
			'role_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Rbac::authorize()->userClass,
			Rbac::authorize()->permitMorphName,
			Rbac::authorize()->permitUsersTable,
			Rbac::authorize()->permitMorphId,
			Rbac::authorize()->permitMorphKey
		);
	}
	
	public function giveToRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roles()->sync($roleModels->pluck('id'));
		$this->load('roles');
		return $this;
	}
	
	public function removeFromRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roles()->detach($roleModels->pluck('id'));
		$this->load('roles');
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
	
	public function inRoles($roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		foreach ($roleModels as $model) {
			if (! $this->roles->containsStrict('id', $model->id)) {
				return false;
			}
		}
		return true;
	}
	
	public function inUsers($users)
	{
		$userModels = Rbac::authorize()->getUserModels($users);
		foreach ($userModels as $model) {
			if (! $this->users->containsStrict('id', $model->id)) {
				return false;
			}
		}
		return true;
	}
}
