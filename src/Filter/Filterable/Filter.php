<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableDefinition;

class Filter
{
    public static function all(): UnrestrictedFilterableList
    {
        return new UnrestrictedFilterableList();
    }

    public static function allow(FilterableDefinition ...$allowedDefinitions): RestrictedFilterableList
    {
        return new RestrictedFilterableList(...$allowedDefinitions);
    }

    public static function column(string $target, array $types): FilterableColumn
    {
        return new FilterableColumn($target, $types);
    }

    public static function relation(string $target, array $types, FilterableDefinition ...$filterableDefinitions): FilterableRelation
    {
        return new FilterableRelation($target, $types, $filterableDefinitions);
    }
}
