<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\TargetedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter\CanBeRequired;
use IndexZer0\EloquentFiltering\Utilities\ClassUtils;

class AllowedMorphRelation implements
    AllowedFilter,
    TargetedFilter,
    RequireableFilter,
    DefinesAllowedChildFilters
{
    use CanBeRequired;

    protected Collection $includeRelationFields;

    public function __construct(
        protected Target            $target,
        protected AllowedTypes      $types,
        protected AllowedFilterList $allowedFilterList,
    ) {
        $this->includeRelationFields = collect();
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): AllowedFilterList
    {
        return $this->allowedFilterList;
    }

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        if (!$pendingFilter->is(FilterContext::MORPH_RELATION)) {
            return null;
        }

        if (!$this->target->isFor($pendingFilter->desiredTarget())) {
            return null;
        }

        return $this->types->get($pendingFilter->requestedFilter());
    }

    public function getTarget(PendingFilter $pendingFilter): Target
    {
        return $this->target;
    }

    public function getIdentifier(): string
    {
        return "\"{$this->target->target()}\"";
    }

    /*
     * -----------------------------
     * Specific methods
     * -----------------------------
     */

    public function includeRelationFields(array $types): self
    {
        $this->includeRelationFields = collect($types);
        return $this;
    }

    public function resolveAllowedFilters(string $modelFqcn): void
    {
        if ($this->includeRelationFields->isEmpty()) {
            return;
        }

        foreach ($this->includeRelationFields as $relationType) {

            $class = Relation::getMorphedModel($relationType);

            $this->allowedFilters()->add(
                Filter::morphType(
                    $relationType,
                    Filter::only(
                        ...$this->getModelsAllowedFilters(new $class())->getAllowedFields()
                    )
                )
            );
        }

        $this->includeRelationFields([]);
    }

    private function getModelsAllowedFilters(Model $model): ?AllowedFilterList
    {
        if (!ClassUtils::modelIsFilterable($model)) {
            return null;
        }

        /** @var IsFilterable $model */
        return $model->allowedFilters();
    }
}
