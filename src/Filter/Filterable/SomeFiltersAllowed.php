<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedMorphRelation;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedRelation;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;

class SomeFiltersAllowed implements AllowedFilterList
{
    protected Collection $allowedFilters;

    public function __construct(AllowedFilter ...$allowedFilters)
    {
        $this->allowedFilters = collect($allowedFilters);
    }

    public function ensureAllowed(PendingFilter $pendingFilter): FilterMethod
    {
        // These are filters such as '$or' and '$and'.
        if ($pendingFilter->is(FilterContext::CONDITION)) {
            return $pendingFilter
                ->getCustomFilterParser()
                ->parse($pendingFilter, allowedFilterList: $this);
        }

        foreach ($this->allowedFilters as $allowedFilter) {
            $allowedType = $allowedFilter->getAllowedType($pendingFilter);

            if ($allowedType) {
                $pendingFilter->validate($allowedType->rules);
                $allowedFilter->markMatched();

                return $pendingFilter
                    ->getCustomFilterParser()
                    ->parse($pendingFilter, $allowedFilter);
            }
        }

        throw new DeniedFilterException($pendingFilter);
    }

    public function resolveRelationsAllowedFilters(string $modelFqcn): self
    {
        /** @var AllowedRelation|AllowedMorphRelation $allowedRelation */
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

    public function getAll(): array
    {
        return $this->allowedFilters->toArray();
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
                fn (AllowedFilter $allowedFilter) => $allowedFilter instanceof AllowedRelation ||
                    $allowedFilter instanceof AllowedMorphRelation
            )
            ->toArray();
    }

    public function getUnmatchedRequiredFiltersIdentifiers(bool $parentWasMatched): Collection
    {
        $unmatchedRequiredFiltersIdentifiers = collect();

        foreach ($this->allowedFilters as $allowedFilter) {
            $identifier = $allowedFilter->getIdentifier();

            if (
                $allowedFilter instanceof RequireableFilter
                && $allowedFilter->isRequired()
                && !$allowedFilter->hasBeenMatched()
            ) {
                if (!$allowedFilter->isScoped() || $parentWasMatched) {
                    $unmatchedRequiredFiltersIdentifiers->push($identifier);
                }
            }

            $beenMatched = $allowedFilter->hasBeenMatched();

            if ($allowedFilter instanceof DefinesAllowedChildFilters) {
                $unmatchedRequiredFiltersIdentifiers = $unmatchedRequiredFiltersIdentifiers->merge(
                    $allowedFilter->allowedFilters()
                        ->getUnmatchedRequiredFiltersIdentifiers($beenMatched)
                        ->map(
                            fn ($requiredFilterIdentifier) => "{$identifier} -> {$requiredFilterIdentifier}"
                        )
                );
            }
        }

        return $unmatchedRequiredFiltersIdentifiers;
    }
}
