<?php

namespace Rainsens\Permit\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleContract
{
    /**
     * All permits associated with certain role.
     */
    public function permits(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a role by its id and guard name.
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Determine if the user had given permit.
     */
    public function hasPermit($permit): bool;
}
