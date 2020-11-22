<?php

namespace Rainsens\Permit\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleContract
{
    /**
     * All permissions associated with current role.
     */
    public function permissions(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a role by its id and guard name.
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Determine if the user had given permission.
     */
    public function hasPermit($permit): bool;
}
