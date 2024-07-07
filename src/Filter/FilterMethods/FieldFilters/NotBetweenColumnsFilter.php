<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Rules\TargetRules;

class NotBetweenColumnsFilter extends BetweenColumnsFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::NOT_BETWEEN_COLUMNS->value;
    }

    public static function format(): array
    {
        return [
            ...TargetRules::get(),
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', 'string'],
        ];
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
