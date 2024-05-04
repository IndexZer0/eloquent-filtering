<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

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

    public static function field(string $target): SortableField
    {
        return new SortableField($target);
    }

    /* public static function relation(string $target, array $types, FilterableDefinition ...$filterableDefinitions): FilterableRelation
     {
         return new FilterableRelation($target, $types, $filterableDefinitions);
     }*/
}
