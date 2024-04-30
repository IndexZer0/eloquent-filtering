<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractJsonColumnFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

class JsonContainsFilter extends AbstractJsonColumnFilter
{
    public function __construct(
        protected string $target,
        protected string|float|int $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$jsonContains';
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', new WhereValue()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereJsonContains(
            $this->target,
            $this->value,
            not: $this->not()
        );
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function not(): bool
    {
        return false;
    }
}
