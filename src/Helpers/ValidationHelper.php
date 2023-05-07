<?php

namespace JoaoRoyer\PhpAsync\Helpers;

use InvalidArgumentException;

class ValidationHelper
{
    public static function throwIfNotCallable($callable)
    {
        if (! is_callable($callable)) {
            throw new InvalidArgumentException('Param must be array of callable');
        }
    }
}
