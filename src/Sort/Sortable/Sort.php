<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Target\AliasedTarget;
use IndexZer0\EloquentFiltering\Target\Target;

class Sort
{
    /*
     * -------------------------
     * AllowedSortList
     * -------------------------
     */

    public static function none(): NoSortsAllowed
    {
        return new NoSortsAllowed();
    }

    public static function all(): AllSortsAllowed
    {
        return new AllSortsAllowed();
    }

    public static function only(SortableField ...$sortableFields): SomeSortsAllowed
    {
        return new SomeSortsAllowed(...$sortableFields);
    }

    /*
     * -------------------------
     * AllowedSort
     * -------------------------
     */

    public static function field(string|AliasedTarget $target): SortableField
    {
        return new SortableField(self::createAlias($target));
    }

    /* public static function relation(string $target, array $types, FilterableDefinition ...$filterableDefinitions): FilterableRelation
     {
         return new FilterableRelation($target, $types, $filterableDefinitions);
     }*/

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
}
