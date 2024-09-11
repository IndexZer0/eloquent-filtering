<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Types;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllTypesAllowed;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\SomeTypesAllowed;
use IndexZer0\EloquentFiltering\Filter\FilterType;

class Types
{
    public static function all(): AllTypesAllowed
    {
        return new AllTypesAllowed();
    }

    public static function only(array $types): SomeTypesAllowed
    {
        return new SomeTypesAllowed(...self::normalizeTypes($types));
    }

    private static function normalizeTypes(array $types): array
    {
        return collect($types)->map(function ($type) {
            if ($type instanceof FilterType) {
                return $type->toAllowedType();
            }
            if (is_string($type)) {
                return new AllowedType($type);
            }
            return $type;
        })->toArray();
    }
}
