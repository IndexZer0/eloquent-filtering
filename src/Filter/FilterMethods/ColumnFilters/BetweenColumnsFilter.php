<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;

class BetweenColumnsFilter extends AbstractColumnFilter
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
        return '$betweenColumns';
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['required', 'array', 'size:2'],
            'value.*' => ['required', 'string'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereBetweenColumns($this->target, $this->value, not: $this->not());
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
