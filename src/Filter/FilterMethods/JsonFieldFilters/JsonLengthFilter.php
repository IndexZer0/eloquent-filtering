<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonFieldFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractJsonFieldFilter;
use IndexZer0\EloquentFiltering\Rules\StrictInteger;

class JsonLengthFilter extends AbstractJsonFieldFilter
{
    public function __construct(
        protected Target $target,
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

    public function apply(Builder $query): Builder
    {
        return $query->whereJsonLength(
            $this->target->getReal(),
            $this->operator,
            $this->value,
        );
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target(),
            $approvedFilter->data_get('operator'),
            $approvedFilter->data_get('value'),
        );
    }
}
