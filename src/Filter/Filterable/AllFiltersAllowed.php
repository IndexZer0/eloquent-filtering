<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Traits\EnsuresChildFiltersAllowed;
use IndexZer0\EloquentFiltering\Target\AliasedRelationTarget;
use IndexZer0\EloquentFiltering\Target\Target;

class AllFiltersAllowed implements AllowedFilterList
{
    use EnsuresChildFiltersAllowed;

    protected Collection $targets;

    public function __construct(TargetContract ...$targets)
    {
        $this->targets = collect($targets)->keyBy(
            fn (TargetContract $target) => $target->target()
        );
    }

    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        if ($pendingFilter->is(FilterContext::CONDITION)) {
            // These are filters such as '$or' and '$and'.
            return $pendingFilter->approveWith(
                childFilters: $this->ensureChildFiltersAllowed($pendingFilter, $this)
            );
        }

        $allowedFilterList = $this;

        $desiredTarget = $pendingFilter->desiredTarget();
        $target = null;

        if ($desiredTarget !== null) {
            $target = $this->targets->get($desiredTarget) ?? Target::alias($desiredTarget);
        }

        if ($target instanceof AliasedRelationTarget) {
            $allowedFilterList = new self(...$target->getChildTargets());
        }

        $childFilters = $this->ensureChildFiltersAllowed($pendingFilter, $allowedFilterList);

        return $pendingFilter->approveWith(
            $target,
            $childFilters
        );
    }

    public function resolveRelationsAllowedFilters(string $modelFqcn): self
    {
        return $this;
    }

    public function add(AllowedFilter ...$allowedFilters): AllowedFilterList
    {
        return $this;
    }

    public function getAllowedFields(): array
    {
        return [];
    }

    public function getAllowedRelations(): array
    {
        return [];
    }

    public function getUnmatchedRequiredFilters(): Collection
    {
        return collect();
    }
}
