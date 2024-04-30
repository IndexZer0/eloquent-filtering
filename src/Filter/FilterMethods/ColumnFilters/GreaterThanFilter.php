<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class GreaterThanFilter extends WhereFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$gt';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '>';
    }
}
