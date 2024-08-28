<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Contracts\Database\Query\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

class JsonContainsFilter extends AbstractFieldFilter
{
    final public function __construct(
        protected string $target,
        protected string|float|int $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$jsonContains';
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', new WhereValue()],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->data_get('value'),
        );
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereJsonContains(
            $this->target,
            $this->value,
            not: $this->not()
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
