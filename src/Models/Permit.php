<?php
namespace Rainsens\Permit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rainsens\Permit\Contracts\PermitContract;

class Permit extends Model implements PermitContract
{
	protected $guarded = ['id'];
	
	public function __construct(array $attributes = [])
	{
		$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
		
		parent::__construct($attributes);
	}
	
	public static function create(array $attributes = [])
	{
	
	}
	
	public function roles(): BelongsToMany
	{
	
	}
	
	public function users(): BelongsToMany
	{
	
	}
	
	public static function findByName(string $name, $guardName): PermitContract
	{
	
	}
	
	public static function findById(int $id, $guardName): PermitContract
	{
	
	}
	
	protected static function getPermits(array $params = []): Collection
	{
	
	}
}
