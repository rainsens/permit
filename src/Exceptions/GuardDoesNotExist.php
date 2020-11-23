<?php

namespace Rainsens\Permit\Exceptions;

use InvalidArgumentException;
use Illuminate\Support\Collection;

class GuardDoesNotExist extends InvalidArgumentException
{
    public static function create(string $givenGuard, Collection $expectedGuards)
    {
        return new static("The given role or permit should use guard `{$expectedGuards->implode(', ')}` instead of `{$givenGuard}`.");
    }
}
