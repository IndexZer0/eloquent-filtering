<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class InFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public array $value,
    ) {

    }

    public static function type(): string
    {
        return '$in';
    }

    protected function not(): bool
    {
        return false;
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->whereIn($this->target, $this->value, not: $this->not());
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'array'],
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
