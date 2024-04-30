<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class LikeStartFilter extends LikeFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$like:start';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function valueBefore(): string
    {
        return '';
    }
}
