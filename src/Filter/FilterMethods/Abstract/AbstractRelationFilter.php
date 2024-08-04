<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Rules\TargetRules;

abstract class AbstractRelationFilter extends AbstractFieldFilter implements HasChildFilters
{
    final public function __construct(
        protected string           $target,
        protected FilterCollection $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function context(): FilterContext
    {
        return FilterContext::RELATION;
    }

    public static function format(): array
    {
        return [
            ...TargetRules::get(),
            'value'   => ['array'],
            'value.*' => ['array'],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->childFilters(),
        );
    }

    public static function childFiltersKey(): string
    {
        return 'value';
    }
}
