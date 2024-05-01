<?php

namespace IndexZer0\EloquentFiltering\Filter\Types;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllTypesAllowed;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\SomeTypesAllowed;

class Types
{
    public static function all(): AllTypesAllowed
    {
        return new AllTypesAllowed();
    }

    public static function only(array $types): SomeTypesAllowed
    {
        return new SomeTypesAllowed($types);
    }
}
