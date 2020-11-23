<?php
namespace Rainsens\Permit\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HasRoles
{
	public function roles(): BelongsToMany
	{
		return $this->morphToMany();
	}
	
	public function hasRole($roles, string $guard = null): bool
	{
		if (is_string($roles)) {
			return $guard
				? $this->roles->contains()
		}
	}
	
	public function hasAnyRole()
	{
	
	}
}
