<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotEqualFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$notEq';
    }

    protected function operator(): string
    {
        return '!=';
    }
}
