<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;

readonly class NotBetweenFilter extends BetweenFilter
{
    public static function type(): string
    {
        return '$notBetween';
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->whereBetween($this->target, $this->value, not: true);
    }
}
