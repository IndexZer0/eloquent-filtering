<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter\CanBeRequired;

class AllowedCustomFilter implements AllowedFilter, RequireableFilter
{
    use CanBeRequired;

    public function __construct(protected AllowedType $type)
    {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        if (!$pendingFilter->is(FilterContext::CUSTOM)) {
            return null;
        }

        if ($this->type->matches($pendingFilter->requestedFilter())) {
            return $this->type;
        }

        return null;
    }

    public function getIdentifier(): string
    {
        return "\"{$this->type->type}\"";
    }

    public function getDescription(): string
    {
        return sprintf('"%s" filter', $this->type->type);
    }
}
