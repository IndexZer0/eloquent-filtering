<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class GreaterThanFilter extends EqualFilter
{
    protected function operator(): string
    {
        return '>';
    }

    public static function type(): string
    {
        return '$gt';
    }
}
