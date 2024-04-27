<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LessThanEqualToFilter extends EqualFilter
{
    protected function operator(): string
    {
        return '<=';
    }

    public static function type(): string
    {
        return '$lte';
    }
}
