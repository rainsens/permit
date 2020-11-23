<?php
namespace Rainsens\Permit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rainsens\Permit\Contracts\RoleContract;

class Role extends Model implements RoleContract
{
	public function permits(): BelongsToMany
	{
		return $this->morphToMany();
	}
	
	/**
	 * Find a role by its name and guard name.
	 */
	public static function findByName(string $name, $guardName): self
	{
	
	}
	
	/**
	 * Find a role by its id and guard name.
	 */
	public static function findById(int $id, $guardName): self
	{
	
	}
	
	/**
	 * Determine if the user had given permit.
	 */
	public function hasPermit($permit): bool
	{
	
	}
}
