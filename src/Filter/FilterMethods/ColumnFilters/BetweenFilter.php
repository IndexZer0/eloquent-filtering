<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

class BetweenFilter extends AbstractColumnFilter
{
    public function __construct(
        protected string $target,
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
        return '$between';
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', new WhereValue()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereBetween($this->target, $this->value, not: $this->not());
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
