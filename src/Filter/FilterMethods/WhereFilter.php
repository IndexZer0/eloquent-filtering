<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;
use IndexZer0\EloquentFiltering\Rules\Scalar;

abstract readonly class WhereFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public string|float|int $value,
    ) {
    }

    abstract protected function operator(): string;

    protected function value(): string|float|int
    {
        return $this->value;
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->where(
            $this->target(),
            $this->operator(),
            $this->value(),
        );
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
