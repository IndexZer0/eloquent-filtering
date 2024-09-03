<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedCustomFilter;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedMorphRelation;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedMorphType;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Types\Types;
use IndexZer0\EloquentFiltering\Target\AliasedTarget;
use IndexZer0\EloquentFiltering\Target\JsonPathTarget;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Utilities\ClassUtils;
use IndexZer0\EloquentFiltering\Utilities\RelationUtils;

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
            $allowedFilters,
        );
    }

    public static function morphRelation(
        string|AliasedTarget $target,
        array|AllowedTypes   $types,
        AllowedMorphType ...$allowedMorphTypes,
    ): AllowedMorphRelation {
        return new AllowedMorphRelation(
            self::createAlias($target),
            self::createTypes($types),
            new SomeFiltersAllowed(...$allowedMorphTypes)
        );
    }

    public static function morphType(
        string|AliasedTarget $type,
        AllowedFilterList $allowedFilters = new NoFiltersAllowed(),
    ): AllowedMorphType {
        return new AllowedMorphType(
            self::createMorphRelationAlias($type),
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

    public static function createMorphRelationAlias(string|AliasedTarget $target): TargetContract
    {
        // Gives full control to the developer.
        if ($target instanceof AliasedTarget) {
            return $target;
        }

        // "Querying All Related Models" support.
        // https://laravel.com/docs/10.x/eloquent-relationships#querying-all-morph-to-related-models
        if ($target === '*') {
            return Target::alias($target);
        }

        ClassUtils::ensureFqcnIsModel($target);

        if (RelationUtils::existsInMorphMap($target)) {
            $alias = RelationUtils::getMorphAlias($target);
            return Target::alias($alias);
        }

        return Target::alias((new $target())->getTable(), $target);
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
