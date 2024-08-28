<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter\CanBeRequired;

class AllowedMorphType implements
    AllowedFilter,
    RequireableFilter,
    DefinesAllowedChildFilters
{
    use CanBeRequired;

    public function __construct(
        public string $type,
        public AllowedFilterList $allowedFilterList
    ) {
    }

    public function allowedFilters(): AllowedFilterList
    {
        return $this->allowedFilterList;
    }

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        throw new Exception('Not implemented');
    }

    public function getTarget(PendingFilter $pendingFilter): ?Target
    {
        return \IndexZer0\EloquentFiltering\Target\Target::alias($this->type);
    }

    public function getIdentifier(): string
    {
        return "\"{$this->type}\"";
    }

    public function getDescription(): string
    {
        return sprintf('"%s" filter', $this->type);
    }
}
