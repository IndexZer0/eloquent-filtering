<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotLikeFilter extends LikeFilter
{
    protected function operatorPrefix(): string
    {
        return 'NOT';
    }

    public static function type(): string
    {
        return '$notLike';
    }
}
