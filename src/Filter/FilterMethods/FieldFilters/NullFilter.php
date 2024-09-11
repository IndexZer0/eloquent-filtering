<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;

class NullFilter implements FilterMethod, Targetable
{
    use FieldFilter;

    public function __construct(
        protected bool $value,
    ) {

    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::NULL->value;
    }

    public static function format(): array
    {
        return [
            'value' => ['required', 'boolean'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereNull(
            $this->eloquentContext->qualifyColumn($this->target),
            not: !$this->value
        );
    }
}
