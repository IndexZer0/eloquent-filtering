<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedColumn;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedCustomFilter;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedJsonColumn;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

class Filter
{
    public static function allowAll(): UnrestrictedFilterableList
    {
        return new UnrestrictedFilterableList();
    }

    public static function allowOnly(AllowedFilter ...$allowedFilters): RestrictedFilterableList
    {
        return new RestrictedFilterableList(...$allowedFilters);
    }

    public static function column(string $target, array $types, ?string $alias = null): AllowedColumn
    {
        return new AllowedColumn($target, $types, $alias);
    }

    public static function jsonColumn(string $target, array $types): AllowedColumn
    {
        return new AllowedJsonColumn($target, $types);
    }

    public static function relation(
        string $target,
        array $types,
        FilterableList $allowedFilters = new RestrictedFilterableList(),
        ?string $alias = null
    ): AllowedRelation {
        return new AllowedRelation($target, $types, $allowedFilters, $alias);
    }

    public static function custom(array $types): AllowedFilter
    {
        return new AllowedCustomFilter($types);
    }
}
