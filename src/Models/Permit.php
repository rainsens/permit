<?php
namespace Rainsens\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Exceptions\PermitAlreadyExists;
use Rainsens\Rbac\Exceptions\PermitDoesNotExist;
use Rainsens\Rbac\Facades\Rbac;

/**
 * Class Permit
 * @package Rainsens\Rbac\Models
 * @property Collection $roleItems
 */
class Permit extends Model implements PermitContract
{
	protected $guarded = ['id'];
	
	public static $methodMaps = [
		'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'
	];
	
	protected $casts = [
		'path' => 'json'
	];
	
	public function getTable()
	{
		return config('permits.table_names.permits', parent::getTable());
	}
	
	public static function create(string $permitName)
	{
		$attributes['name'] = $permitName;
		$attributes['guard'] = Rbac::guard()->name;;
		
		if (static::where($attributes)->first()) {
			throw new PermitAlreadyExists("Permit provided already exists.");
		}
		return static::query()->create($attributes);
	}
	
	public static function findByName(string $name)
	{
		$permit = static::where(['name' => $name, 'guard' => Rbac::guard()->name])->first();
		
		if (! $permit) {
			throw new PermitDoesNotExist('Permit name provided does not exist.');
		}
		
		return $permit;
	}
	
	public static function findById(int $id)
	{
		$permit = static::where(['id' => $id, 'guard' => Rbac::guard()->name])->first();
		
		if (! $permit) {
			throw new PermitDoesNotExist('Permit id provided does not exist.');
		}
		
		return $permit;
	}
	
	public function roleItems()
	{
		return $this->belongsToMany(
			Rbac::authorize()->roleClass,
			Rbac::authorize()->permitRolesTable,
			'permit_id', 'role_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Rbac::authorize()->userClass, 'permitable'
		);
	}
	
	public function givePermitToRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roleItems()->sync($roleModels->pluck('id'));
		$this->load('roleItems');
		return $this;
	}
	
	public function removePermitToRoles(...$roles)
	{
		$roleModels = Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $roles);
		$this->roleItems()->detach($roleModels->pluck('id'));
		$this->load('roleItems');
		return $this;
	}
	
	public function hasRoleItem($role)
	{
		$roleModel = (Rbac::authorize()->getPermitOrRoleModels(Rbac::authorize()->roleInstance, $role))[0];
		return $this->roleItems->containsStrict('id', $roleModel->id);
	}
}
