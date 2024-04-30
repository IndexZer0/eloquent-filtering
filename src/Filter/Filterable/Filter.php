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
    /*
     * -------------------------
     * FilterableList
     * -------------------------
     */

    public static function allowNone(): NoFiltersAllowed
    {
        return new NoFiltersAllowed();
    }

    public static function allowAll(): AllFiltersAllowed
    {
        return new AllFiltersAllowed();
    }

    public static function allowOnly(AllowedFilter ...$allowedFilters): SomeFiltersAllowed
    {
        return new SomeFiltersAllowed(...$allowedFilters);
    }

    /*
     * -------------------------
     * AllowedFilter
     * -------------------------
     */

    public static function column(string $target, array $types, ?string $alias = null): AllowedColumn
    {
        return new AllowedColumn($target, $types, $alias);
    }

    public static function jsonColumn(string $target, array $types): AllowedJsonColumn
    {
        return new AllowedJsonColumn($target, $types);
    }

    public static function relation(
        string $target,
        array $types,
        FilterableList $allowedFilters = new NoFiltersAllowed(),
        ?string $alias = null
    ): AllowedRelation {
        return new AllowedRelation($target, $types, $allowedFilters, $alias);
    }

    public static function custom(array $types): AllowedCustomFilter
    {
        return new AllowedCustomFilter($types);
    }
}
