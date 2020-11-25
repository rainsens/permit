<?php
namespace Rainsens\Authorize\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rainsens\Authorize\Contracts\PermitContract;
use Rainsens\Authorize\Exceptions\PermitAlreadyExists;
use Rainsens\Authorize\Exceptions\PermitDoesNotExist;
use Rainsens\Authorize\Facades\Authorize;

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
		$attributes['guard'] = Authorize::guardName();
		
		if (static::where($attributes)->first()) {
			throw new PermitAlreadyExists("Permit provided already exists.");
		}
		return static::query()->create($attributes);
	}
	
	public static function findByName(string $name)
	{
		$permit = static::where(['name' => $name, 'guard' => Authorize::guardName()])->first();
		
		if (! $permit) {
			throw new PermitDoesNotExist('Permit name provided does not exist.');
		}
		
		return $permit;
	}
	
	public static function findById(int $id)
	{
		$permit = static::where(['id' => $id, 'guard' => Authorize::guardName()])->first();
		
		if (! $permit) {
			throw new PermitDoesNotExist('Permit id provided does not exist.');
		}
		
		return $permit;
	}
	
	public function roleItems()
	{
		return $this->belongsToMany(
			Authorize::roleClass(), Authorize::permitRolesTable(), 'permit_id', 'role_id'
		);
	}
	
	public function users(): BelongsToMany
	{
		return $this->morphedByMany(
			Authorize::authClass(), 'permitable'
		);
	}
	
	public function givePermitToRoles(...$roles)
	{
		$roleModels = Authorize::getPermitOrRoleModels(Authorize::roleInstance(), $roles);
		$this->roleItems()->sync($roleModels->pluck('id'));
		$this->load('roleItems');
		return $this;
	}
	
	public function removePermitToRoles(...$roles)
	{
		$roleModels = Authorize::getPermitOrRoleModels(Authorize::roleInstance(), $roles);
		$this->roleItems()->detach($roleModels->pluck('id'));
		$this->load('roleItems');
		return $this;
	}
	
	public function hasRoleItem($role)
	{
		$roleModel = (Authorize::getPermitOrRoleModels(Authorize::roleInstance(), $role))[0];
		return $this->roleItems->containsStrict('id', $roleModel->id);
	}
}
