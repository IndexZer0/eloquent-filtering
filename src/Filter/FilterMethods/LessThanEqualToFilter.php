<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LessThanEqualToFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$lte';
    }

    protected function operator(): string
    {
        return '<=';
    }
}
