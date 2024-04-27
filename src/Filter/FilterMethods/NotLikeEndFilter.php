<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotLikeEndFilter extends NotLikeFilter
{
    protected function valueAfter(): string
    {
        return '';
    }

    public static function type(): string
    {
        return '$notLike:end';
    }
}
