<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

class BetweenFilter implements FilterMethod, Targetable
{
    use FieldFilter;

    public function __construct(
        protected array $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::BETWEEN->value;
    }

    public static function format(): array
    {
        return [
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', new WhereValue()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereBetween(
            $this->eloquentContext->qualifyColumn($this->target),
            $this->value,
            not: $this->not(),
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
