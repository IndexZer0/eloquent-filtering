<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class NotInFilter extends InFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$notIn';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function not(): bool
    {
        return true;
    }
}
