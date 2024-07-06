<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

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
        return $this->hasModifier('start') ? '' : '%';
    }

    protected function valueAfter(): string
    {
        return $this->hasModifier('end') ? '' : '%';
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
