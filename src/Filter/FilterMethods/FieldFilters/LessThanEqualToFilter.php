<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use IndexZer0\EloquentFiltering\Filter\FilterType;

class LessThanEqualToFilter extends WhereFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::LESS_THAN_EQUAL_TO->value;
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '<=';
    }
}
