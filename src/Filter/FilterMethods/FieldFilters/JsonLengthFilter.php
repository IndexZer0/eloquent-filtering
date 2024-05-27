<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;
use IndexZer0\EloquentFiltering\Rules\StrictInteger;

class JsonLengthFilter extends AbstractFieldFilter
{
    final public function __construct(
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
            'value'    => ['required', new StrictInteger(), ],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->data_get('operator'),
            $approvedFilter->data_get('value'),
        );
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
