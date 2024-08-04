<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\RelationFilters;

use IndexZer0\EloquentFiltering\Filter\FilterType;

class DoesntHasFilter extends HasFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::DOESNT_HAS->value;
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '<';
    }
}
