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

    public static function only(SortableColumn ...$sortableColumns): SomeSortsAllowed
    {
        return new SomeSortsAllowed(...$sortableColumns);
    }

    /*
     * -------------------------
     * AllowedSort
     * -------------------------
     */

    public static function column(string $target): SortableColumn
    {
        return new SortableColumn($target);
    }

    /* public static function relation(string $target, array $types, FilterableDefinition ...$filterableDefinitions): FilterableRelation
     {
         return new FilterableRelation($target, $types, $filterableDefinitions);
     }*/
}
