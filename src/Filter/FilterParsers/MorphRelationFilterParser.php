<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {
        /** @var TargetedFilter&DefinesAllowedChildFilters $allowedFilter */
        $target = $allowedFilter->getTarget($pendingFilter);
        $relation = $pendingFilter->model()->{$target->getReal()}();

        $types = $pendingFilter->data()['types'];

        $morphTypes = new MorphTypes();

        foreach ($types as &$type) {

            $allAllowedMorphTypes = collect($allowedFilter->allowedFilters()->getAll());

            $allowedMorphType = $allAllowedMorphTypes->first(fn (AllowedMorphType $allowedMorphType) => $allowedMorphType->type === $type['type']);
            if ($allowedMorphType === null) {
                throw new DeniedFilterException($pendingFilter);
            }

            if ($type['type'] === '*') {
                $model = $pendingFilter->model();
                $polymorphicTypes = $model->newModelQuery()->distinct()->pluck($relation->getMorphType())->filter()->all();

                foreach ($polymorphicTypes as $polymorphicType) {
                    $modelClass = Relation::getMorphedModel($polymorphicType);

                    $filters = $this->parseMorphTypesChildFilters(
                        new $modelClass(),
                        $type,
                        $allowedMorphType,
                    );
                    $morphTypes->push(new MorphType(
                        $polymorphicType,
                        $filters
                    ));
                }
            } else {
                $modelClass = Relation::getMorphedModel($type['type']);

                $model = $modelClass ? new $modelClass() : $pendingFilter->model();

                $filters = $this->parseMorphTypesChildFilters($model, $type, $allowedMorphType);

                $morphTypes->push(new MorphType(
                    $type['type'],
                    $filters
                ));
            }
        }

        return (new FilterBuilder($pendingFilter))
            ->target($target)
            ->morphTypes($morphTypes)
            ->build(
                new EloquentContext(
                    $pendingFilter->model(),
                    $relation,
                )
            );
    }

    protected function parseMorphTypesChildFilters(Model $model, array $type, $allowedMorphType): FilterCollection
    {
        $filterParser = resolve(FilterParser::class);
        return $filterParser->parse($model, data_get($type, 'value', []), $allowedMorphType->allowedFilters());
    }
}
