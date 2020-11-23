<?php

namespace Rainsens\Permit\Exceptions;

use InvalidArgumentException;

class PermitDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permitName, string $guardName = '')
    {
        return new static("There is no permit named `{$permitName}` for guard `{$guardName}`.");
    }

    public static function withId(int $permitId, string $guardName = '')
    {
        return new static("There is no [permit] with id `{$permitId}` for guard `{$guardName}`.");
    }
}
