<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedCustomFilter;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Types\Types;
use IndexZer0\EloquentFiltering\Target\AliasedTarget;
use IndexZer0\EloquentFiltering\Target\JsonPathTarget;
use IndexZer0\EloquentFiltering\Target\Target;

class Filter
{
    /*
     * -------------------------
     * AllowedFilterList
     * -------------------------
     */

    public static function none(): NoFiltersAllowed
    {
        return new NoFiltersAllowed();
    }

    public static function only(AllowedFilter ...$allowedFilters): SomeFiltersAllowed
    {
        return new SomeFiltersAllowed(...$allowedFilters);
    }

    /*
     * -------------------------
     * AllowedFilter
     * -------------------------
     */

    public static function field(string|AliasedTarget $target, array|AllowedTypes $types): AllowedField
    {
        return new AllowedField(
            self::createAlias($target),
            self::createTypes($types)
        );
    }

    public static function relation(
        string|AliasedTarget $target,
        array|AllowedTypes $types,
        AllowedFilterList $allowedFilters = new NoFiltersAllowed(),
    ): AllowedRelation {
        return new AllowedRelation(
            self::createAlias($target),
            self::createTypes($types),
            $allowedFilters
        );
    }

    public static function custom(string|AllowedType $type): AllowedCustomFilter
    {
        return new AllowedCustomFilter(self::createType($type));
    }

    /*
     * -------------------------
     * Target
     * -------------------------
     */

    private static function createAlias(string|AliasedTarget $target): TargetContract
    {
        if (!is_string($target)) {
            return $target;
        }

        if (str_contains($target, '->')) {
            return new JsonPathTarget($target);
        }

        return Target::alias($target);
    }

    /*
     * -------------------------
     * Types
     * -------------------------
     */

    private static function createTypes(array|AllowedTypes $types): AllowedTypes
    {
        if (is_array($types)) {
            return Types::only($types);
        }

        return $types;
    }

    private static function createType(string|AllowedType $type): AllowedType
    {
        if (is_string($type)) {
            return new AllowedType($type);
        }

        return $type;
    }
}
