<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class GreaterThanFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$gt';
    }

    protected function operator(): string
    {
        return '>';
    }
}
