<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class NotLikeEndFilter extends NotLikeFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$notLike:end';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function valueAfter(): string
    {
        return '';
    }
}
