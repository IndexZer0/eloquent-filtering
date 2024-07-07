<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Utilities\RelationUtils;

class AllowedRelation implements AllowedFilter
{
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

    public function getTarget(PendingFilter $pendingFilter): ?Target
    {
        return $this->target;
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
        if (!RelationUtils::relationMethodExists(
            $relationMethod = $this->target->getReal(),
            $modelFqcn
        )
        ) {
            return null;
        }

        $relationModel = RelationUtils::getRelationModel($modelFqcn, $relationMethod);

        $relatedModelsAllowedFilters = $this->getModelsAllowedFilters($relationModel);

        if ($relatedModelsAllowedFilters !== null) {
            $this->allowedFilters = $this->allowedFilters->add(
                ...$relatedModelsAllowedFilters->getAllowedFields()
            );
        }

        return $relationModel::class;
    }

    private function getModelsAllowedFilters(Model $model): ?AllowedFilterList
    {
        if (!$this->modelIsFilterable($model)) {
            return null;
        }

        /** @var IsFilterable $model */
        return $model->allowedFilters();
    }

    private function modelIsFilterable(Model $model): bool
    {
        return is_a($model, IsFilterable::class);
    }
}
