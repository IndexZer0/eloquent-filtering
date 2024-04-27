<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class NullFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public bool $value,
    ) {

    }

    public static function type(): string
    {
        return '$null';
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->whereNull($this->target, not: !$this->value);
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'boolean'],
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
