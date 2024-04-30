<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;

class NullFilter extends AbstractColumnFilter
{
    public function __construct(
        protected string $target,
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
        return '$null';
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'boolean'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereNull($this->target, not: !$this->value);
    }
}
