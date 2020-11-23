<?php

namespace Rainsens\Permit\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermitContract
{
    /**
     * All roles associated with certain permit.
     */
    public function roles(): BelongsToMany;
	
	/**
	 * Find a permit by its id.
	 */
	public static function findById(int $id, $guardName): self;
    
    /**
     * Find a permit by its name.
     */
    public static function findByName(string $name, $guardName): self;

    
}
