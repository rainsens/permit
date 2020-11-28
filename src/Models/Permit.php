<?php
namespace Rainsens\Rbac\Models;

use Rainsens\Rbac\Facades\Rbac;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Exceptions\PermitAlreadyExists;
use Rainsens\Rbac\Exceptions\InvalidArgumentException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Permit
 * @package Rainsens\Rbac\Models
 * @property Collection $roles
 * @property Collection $users
 * @property $guard
 */
class Permit extends Model implements PermitContract
{
	protected $guarded = ['id'];
	
	protected $casts = [
		'path' => 'array',
		'method' => 'array',
	];
	
	public function getTable()
	{
		return config('permits.tables.permits', parent::getTable());
	}
	
	protected static function boot()
	{
		parent::boot();
		static::deleting(function (Permit $model) {
			$model->roles()->detach();
		});
	}
	
	public function setPathAttribute($value)
	{
		$this->attributes['path'] = collect($value)
			->flatten()
			->map(function ($path) {
				return trim(str_replace('\/', '/', $path), '/') ?? '/';
			})->toJson();
	}
	
	public function setMethodAttribute($value)
	{
		$this->attributes['method'] = collect($value)
			->flatten()
			->map(function ($method) {
				$method = strtoupper($method);
				if (in_array($method, PermitContract::HTTP_METHODS)) {
					return $method;
				}
				return null;
			})->toJson();
	}
	
	public static function create(array $attributes)
	{
		if (! isset($attributes['name']) or ! isset($attributes['slug'])) {
			throw new InvalidArgumentException('Permit name and slug both are required.');
		}
		
		$attributes['guard'] = Rbac::guard()->name;
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
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function giveToRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$expectedButValidRoles = Rbac::guard()->examine($expectedRoles, $this->guard);
		$this->roles()->sync($expectedButValidRoles);
		$this->load('roles');
		return $this;
	}
	
	/**
	 * Find by 'id' and 'slug'
	 */
	public function removeFromRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$ExpectedButValidRoles = Rbac::guard()->examine($expectedRoles, $this->guard);
		$this->roles()->detach($ExpectedButValidRoles->pluck('id'));
		$this->load('roles');
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
	public function inRoles(...$roles)
	{
		$expectedRoles = Rbac::supplier()->findExpectedModels(Rbac::authorize()->roleInstance, $roles);
		$expectedButValidRoles = Rbac::guard()->examine($expectedRoles, $this->guard);
		$valid = $expectedButValidRoles->pluck('id')->toArray();
		$actual = $this->roles->pluck('id')->toArray();
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
