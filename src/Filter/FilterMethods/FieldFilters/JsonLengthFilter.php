<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;
use IndexZer0\EloquentFiltering\Rules\StrictInteger;

class JsonLengthFilter implements FilterMethod, Targetable
{
    use FieldFilter;

    public function __construct(
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
        return FilterType::JSON_LENGTH->value;
    }

    public static function format(): array
    {
        return [
            'operator' => ['required', Rule::in(['=', '<', '<=', '>', '>='])],
            'value'    => ['required', new StrictInteger()],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereJsonLength(
            $this->eloquentContext->qualifyColumn($this->target),
            $this->operator,
            $this->value,
        );
    }
}
