<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\Target\AliasedTarget;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedCustomFilter;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedJsonField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Target\JsonPathTarget;
use IndexZer0\EloquentFiltering\Filter\Target\Target;
use IndexZer0\EloquentFiltering\Filter\Types\Types;

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

    public static function all(): AllFiltersAllowed
    {
        return new AllFiltersAllowed();
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

    public static function jsonField(string $target, array|AllowedTypes $types): AllowedJsonField
    {
        return new AllowedJsonField(
            new JsonPathTarget($target),
            self::createTypes($types)
        );
    }

    public static function relation(
        string|AliasedTarget $target,
        array                $types,
        AllowedFilterList    $allowedFilters = new NoFiltersAllowed(),
    ): AllowedRelation {
        return new AllowedRelation(
            self::createAlias($target),
            self::createTypes($types),
            $allowedFilters
        );
    }

    public static function custom(array|AllowedTypes $types): AllowedCustomFilter
    {
        return new AllowedCustomFilter(self::createTypes($types));
    }

    /*
     * -------------------------
     * Target
     * -------------------------
     */

    private static function createAlias(string|AliasedTarget $target): TargetContract
    {
        if (is_string($target)) {
            return Target::alias($target);
        }

        return $target;
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
}
