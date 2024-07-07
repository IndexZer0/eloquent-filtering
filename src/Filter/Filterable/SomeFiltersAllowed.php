<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Traits\EnsuresChildFiltersAllowed;

class SomeFiltersAllowed implements AllowedFilterList
{
    use EnsuresChildFiltersAllowed;

    protected Collection $allowedFilters;

    public function __construct(AllowedFilter ...$allowedFilters)
    {
        $this->allowedFilters = collect($allowedFilters);
    }

    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        if ($pendingFilter->is(FilterContext::CONDITION)) {
            // These are filters such as '$or' and '$and'.
            return $pendingFilter->approveWith(
                childFilters: $this->ensureChildFiltersAllowed($pendingFilter, $this)
            );
        }

        foreach ($this->allowedFilters as $allowedFilter) {
            if ($allowedFilter->matches($pendingFilter)) {

                $allowedChildFilters = $allowedFilter->allowedFilters();

                $childFilters = $this->ensureChildFiltersAllowed($pendingFilter, $allowedChildFilters);

                return $pendingFilter->approveWith(
                    $allowedFilter->getTarget($pendingFilter),
                    $childFilters
                );
            }
        }

        throw new DeniedFilterException($pendingFilter);
    }

    public function resolveRelationsAllowedFilters(string $modelFqcn): self
    {
        /** @var AllowedRelation $allowedRelation */
        foreach ($this->getAllowedRelations() as $allowedRelation) {
            $allowedRelation->resolveAllowedFilters($modelFqcn);
        }
        return $this;
    }

    public function add(AllowedFilter ...$allowedFilters): AllowedFilterList
    {
        $this->allowedFilters->push(...$allowedFilters);
        return $this;
    }

    public function getAllowedFields(): array
    {
        return $this->allowedFilters
            ->filter(
                fn (AllowedFilter $allowedFilter) => $allowedFilter instanceof AllowedField
            )
            ->toArray();
    }

    public function getAllowedRelations(): array
    {
        return $this->allowedFilters
            ->filter(
                fn (AllowedFilter $allowedFilter) => $allowedFilter instanceof AllowedRelation
            )
            ->toArray();
    }

    public function getUnmatchedRequiredFilters(): Collection
    {
        $unmatchedRequiredFilters = collect();

        foreach ($this->allowedFilters as $allowedFilter) {
            if ($allowedFilter->isRequired() && !$allowedFilter->hasBeenMatched()) {
                $unmatchedRequiredFilters->push($allowedFilter);
            }

            $unmatchedRequiredFilters = $unmatchedRequiredFilters->merge(
                $allowedFilter->allowedFilters()->getUnmatchedRequiredFilters()
            );
        }

        return $unmatchedRequiredFilters;
    }
}
