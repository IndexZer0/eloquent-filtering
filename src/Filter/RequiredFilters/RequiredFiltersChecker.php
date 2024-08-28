<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\RequiredFilters;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\RequiredFilterException;

class RequiredFiltersChecker
{
    public function __construct(
        protected AllowedFilterList $allowedFilterList,
        protected bool $parentWasMatched,
    ) {
    }

    public function __invoke(): void
    {
        $unmatchedRequiredFiltersIdentifiers = $this->getUnmatchedRequiredFiltersIdentifiers(
            $this->allowedFilterList,
            parentWasMatched: $this->parentWasMatched
        );
        if ($unmatchedRequiredFiltersIdentifiers->isNotEmpty()) {
            throw RequiredFilterException::fromStrings($unmatchedRequiredFiltersIdentifiers);
        }
    }

    public function getUnmatchedRequiredFiltersIdentifiers(
        AllowedFilterList $allowedFilterList,
        bool $parentWasMatched
    ): Collection {
        $unmatchedRequiredFiltersIdentifiers = collect();

        $allowedFilters = $allowedFilterList->getAll();
        foreach ($allowedFilters as $allowedFilter) {
            $identifier = $allowedFilter->getIdentifier();

            if ($this->filterShouldBeAdded($allowedFilter, $parentWasMatched)) {
                $unmatchedRequiredFiltersIdentifiers->push($identifier);
            }

            $beenMatched = $allowedFilter->hasBeenMatched();

            if ($allowedFilter instanceof DefinesAllowedChildFilters) {
                $unmatchedRequiredFiltersIdentifiers = $unmatchedRequiredFiltersIdentifiers->merge(
                    $this->getUnmatchedRequiredFiltersIdentifiers(
                        $allowedFilter->allowedFilters(),
                        $beenMatched
                    )->map(
                        fn ($requiredFilterIdentifier) => "{$identifier} -> {$requiredFilterIdentifier}"
                    )
                );
            }
        }

        return $unmatchedRequiredFiltersIdentifiers;
    }

    protected function filterShouldBeAdded(AllowedFilter $allowedFilter, bool $parentWasMatched): bool
    {
        return $allowedFilter instanceof RequireableFilter &&
            $allowedFilter->isRequired() &&
            !$allowedFilter->hasBeenMatched() &&
            (!$allowedFilter->isScoped() || $parentWasMatched);
    }
}
