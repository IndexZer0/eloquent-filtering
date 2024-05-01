<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Target\Alias;
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

    public static function column(string|Alias $target, array $types): AllowedColumn
    {
        return new AllowedColumn(self::createAlias($target), $types);
    }

    public static function jsonColumn(string $target, array $types): AllowedJsonColumn
    {
        return new AllowedJsonColumn($target, $types);
    }

    public static function relation(
        string|Alias $target,
        array $types,
        FilterableList $allowedFilters = new NoFiltersAllowed(),
    ): AllowedRelation {
        return new AllowedRelation(self::createAlias($target), $types, $allowedFilters);
    }

    public static function custom(array $types): AllowedCustomFilter
    {
        return new AllowedCustomFilter($types);
    }

    private static function createAlias(string|Alias $target): Alias
    {
        if (is_string($target)) {
            return new Alias($target);
        }

        return $target;
    }
}
