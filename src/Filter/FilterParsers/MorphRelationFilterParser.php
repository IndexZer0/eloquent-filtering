<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedMorphType;
use IndexZer0\EloquentFiltering\Filter\Builder\FilterBuilder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\TargetedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\Morph\MorphType;
use IndexZer0\EloquentFiltering\Filter\Morph\MorphTypes;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterParser\EnsuresChildFiltersAllowed;

class MorphRelationFilterParser implements CustomFilterParser
{
    use EnsuresChildFiltersAllowed;

    protected MorphTo $relation;
    protected Collection $allAllowedMorphTypes;

    public function __construct(
        protected MorphTypes $morphTypes = new MorphTypes(),
    ) {
    }

    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {
        /** @var TargetedFilter&DefinesAllowedChildFilters $allowedFilter */
        $target = $allowedFilter->getTarget($pendingFilter);
        $this->relation = $pendingFilter->model()->{$target->getReal()}();
        $this->allAllowedMorphTypes = collect($allowedFilter->allowedFilters()->getAll());

        $types = $pendingFilter->data()['types'];

        foreach ($types as $type) {
            $this->parseType($pendingFilter, $type);
        }

        $filterBuilder = new FilterBuilder(
            $pendingFilter,
            new EloquentContext(
                $pendingFilter->model(),
                $this->relation,
            ),
        );

        return $filterBuilder
            ->target($target)
            ->morphTypes($this->morphTypes)
            ->build();
    }

    protected function parseMorphTypesChildFilters(
        Model $model,
        array $type,
        AllowedMorphType $allowedMorphType,
        PendingFilter $pendingFilter,
    ): FilterCollection {
        $filterParser = resolve(FilterParser::class);
        return $filterParser->parse(
            $model,
            data_get($type, 'value', []),
            $allowedMorphType->allowedFilters(),
            previousPendingFilter: $pendingFilter,
        );
    }

    protected function getModel(string $polymorphicType): Model
    {
        $modelFqcn = Relation::getMorphedModel($polymorphicType);
        // If model fqcn is null, this model is not registered in the morph map.
        // We can assume that the polymorphic type will be the fqcn.
        return $modelFqcn === null ? new $polymorphicType() : new $modelFqcn();
    }

    protected function parseType(PendingFilter $pendingFilter, array $type): void
    {
        $allowedMorphType = $this->allAllowedMorphTypes->first(
            fn (AllowedMorphType $allowedMorphType) => $allowedMorphType->getTarget($pendingFilter)->isFor($type['type']),
        );

        if ($allowedMorphType === null) {
            throw new DeniedFilterException($pendingFilter);
        }

        /** @var AllowedMorphType $allowedMorphType */
        $allowedMorphType->markMatched();

        $morphTypeTarget = $allowedMorphType->getTarget($pendingFilter)->getReal();

        if ($morphTypeTarget === '*') {
            $model = $pendingFilter->model();

            $polymorphicTypes = $model->newModelQuery()
                ->distinct()
                ->pluck($this->relation->getMorphType())
                ->filter()
                ->all();

            foreach ($polymorphicTypes as $polymorphicType) {
                $this->parseMorphType(
                    $polymorphicType,
                    $type,
                    $allowedMorphType,
                    $pendingFilter,
                );
            }
        } else {
            $this->parseMorphType(
                $morphTypeTarget,
                $type,
                $allowedMorphType,
                $pendingFilter,
            );
        }
    }

    protected function parseMorphType(
        string $polymorphicType,
        array $type,
        AllowedMorphType $allowedMorphType,
        PendingFilter $pendingFilter,
    ): void {
        $model = $this->getModel($polymorphicType);

        $filters = $this->parseMorphTypesChildFilters(
            $model,
            $type,
            $allowedMorphType,
            $pendingFilter,
        );
        $this->morphTypes->push(new MorphType(
            $polymorphicType,
            $filters,
        ));
    }
}
