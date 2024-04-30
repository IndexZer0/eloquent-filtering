<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class NotLikeFilter extends LikeFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$notLike';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return 'NOT LIKE';
    }
}
