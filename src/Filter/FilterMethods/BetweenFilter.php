<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

readonly class BetweenFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public array $value,
    ) {
    }

    public static function type(): string
    {
        return '$between';
    }

    protected function not(): bool
    {
        return false;
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->whereBetween($this->target, $this->value, not: $this->not());
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', new WhereValue()],
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
