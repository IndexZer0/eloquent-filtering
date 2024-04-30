<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

class LikeFilter extends WhereFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$like';
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function valueBefore(): string
    {
        return '%';
    }

    protected function valueAfter(): string
    {
        return '%';
    }

    protected function operator(): string
    {
        return 'LIKE';
    }

    protected function value(): string
    {
        return "{$this->valueBefore()}{$this->value}{$this->valueAfter()}";
    }
}
