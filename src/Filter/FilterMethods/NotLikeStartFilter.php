<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotLikeStartFilter extends NotLikeFilter
{
    protected function valueBefore(): string
    {
        return '';
    }

    public static function type(): string
    {
        return '$notLike:start';
    }
}
