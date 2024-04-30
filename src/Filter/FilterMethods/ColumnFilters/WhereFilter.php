<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

abstract class WhereFilter extends AbstractColumnFilter
{
    public function __construct(
        protected string           $target,
        protected string|float|int $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', new WhereValue()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(
            $this->target(),
            $this->operator(),
            $this->value(),
        );
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    abstract protected function operator(): string;

    protected function value(): string|float|int
    {
        return $this->value;
    }

    public function target(): string
    {
        return $this->target;
    }
}
