<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;
use IndexZer0\EloquentFiltering\Rules\Scalar;

readonly class EqualFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public string|float|int $value,
    ) {
    }

    protected function operator(): string
    {
        return '=';
    }

    public static function type(): string
    {
        return '$eq';
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->where($this->target, $this->operator(), $this->value);
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', new Scalar()],
        ];
    }

    public function target(): string
    {
        return $this->target;
    }

    public function hasTarget(): true
    {
        return true;
    }
}
