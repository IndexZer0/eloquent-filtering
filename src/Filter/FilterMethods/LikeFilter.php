<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LikeFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$like';
    }

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
