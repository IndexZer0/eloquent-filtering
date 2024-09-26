<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\RequireableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\TargetedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter\CanBeRequired;
use IndexZer0\EloquentFiltering\Utilities\ClassUtils;
use IndexZer0\EloquentFiltering\Utilities\RelationUtils;

class AllowedRelation implements
    AllowedFilter,
    TargetedFilter,
    RequireableFilter,
    DefinesAllowedChildFilters
{
    use CanBeRequired;

    protected bool $includeRelationFields = false;

    public function __construct(
        protected Target            $target,
        protected AllowedTypes      $types,
        protected AllowedFilterList $allowedFilters,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): AllowedFilterList
    {
        return $this->allowedFilters;
    }

    public function getAllowedType(PendingFilter $pendingFilter): ?AllowedType
    {
        if (!$pendingFilter->is(FilterContext::RELATION)) {
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
        return $this->target->target();
    }

    /*
     * -----------------------------
     * Specific methods
     * -----------------------------
     */

    public function includeRelationFields(bool $include = true): self
    {
        $this->includeRelationFields = $include;
        return $this;
    }

    public function andNestedRelation(AllowedRelation $relation): self
    {
        $this->allowedFilters = $this->allowedFilters->add($relation);
        return $this;
    }

    public function resolveAllowedFilters(string $modelFqcn): void
    {
        if (!$this->includeRelationFields) {
            return;
        }

        $relationModel = $this->resolveRelationsAllowedFields($modelFqcn);

        $this->includeRelationFields(false);

        if ($relationModel !== null) {
            $this->allowedFilters->resolveRelationsAllowedFilters($relationModel);
        }
    }

    protected function resolveRelationsAllowedFields(string $modelFqcn): ?string
    {
        if (
            !RelationUtils::relationMethodExists(
                $relationMethod = $this->target->getReal(),
                $modelFqcn,
            )
        ) {
            return null;
        }

        $model = new $modelFqcn();

        /** @var Relation $query */
        $query = $model->$relationMethod();

        $models = [$relationModel = $query->getRelated()];

        if ($query instanceof BelongsToMany) {
            $pivotClass = $query->getPivotClass();
            $models[] = new $pivotClass();
        }

        $this->addAllowedFiltersFromRelatedModels(...$models);

        return $relationModel::class;
    }

    private function addAllowedFiltersFromRelatedModels(Model ...$models): void
    {
        foreach ($models as $model) {
            $modelsAllowedFilters = ClassUtils::getModelsAllowedFilters($model);

            if ($modelsAllowedFilters !== null) {
                $this->allowedFilters = $this->allowedFilters->add(
                    ...$modelsAllowedFilters->getAllowedFields(),
                );
            }
        }

    }
}
