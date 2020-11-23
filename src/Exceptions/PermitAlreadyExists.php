<?php

namespace Rainsens\Permit\Exceptions;

use InvalidArgumentException;

class PermitAlreadyExists extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName)
    {
        return new static("A `{$permissionName}` permit already exists for guard `{$guardName}`.");
    }
}
