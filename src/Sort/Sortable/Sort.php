<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

class Sort
{
    public static function all(): UnrestrictedSortableList
    {
        return new UnrestrictedSortableList();
    }

    public static function allow(SortableColumn ...$sortableColumns): RestrictedSortableList
    {
        return new RestrictedSortableList(...$sortableColumns);
    }

    public static function column(string $target): SortableColumn
    {
        return new SortableColumn($target);
    }

    /* public static function relation(string $target, array $types, FilterableDefinition ...$filterableDefinitions): FilterableRelation
     {
         return new FilterableRelation($target, $types, $filterableDefinitions);
     }*/
}
