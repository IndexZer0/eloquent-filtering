<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractJsonColumnFilter;
use IndexZer0\EloquentFiltering\Rules\StrictInteger;

class JsonLengthFilter extends AbstractJsonColumnFilter
{
    public function __construct(
        protected string $target,
        protected string $operator,
        protected int    $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$jsonLength';
    }

    public static function format(): array
    {
        return [
            'target'   => ['required', 'string'],
            'operator' => ['required', Rule::in(['=', '<', '<=', '>', '>='])],
            'value'    => ['required', new StrictInteger()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereJsonLength(
            $this->target,
            $this->operator,
            $this->value,
        );
    }
}
