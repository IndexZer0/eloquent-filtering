<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Rules\TargetRules;

class NullFilter extends AbstractFieldFilter
{
    final public function __construct(
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
        return FilterType::NULL->value;
    }

    public static function format(): array
    {
        return [
            ...TargetRules::get(),
            'value' => ['required', 'boolean'],
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
        return $query->whereNull($this->target, not: !$this->value);
    }
}
