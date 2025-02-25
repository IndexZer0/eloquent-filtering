<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use IndexZer0\EloquentFiltering\Filter\Builder\FilterBuilder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\PivotableFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\TargetedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class FieldFilterParser implements CustomFilterParser
{
    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {
        /** @var TargetedFilter $allowedFilter */
        // @phpstan-ignore varTag.nativeType
        $target = $allowedFilter->getTarget($pendingFilter);
        $relation = $pendingFilter->relation();

        if (is_a($allowedFilter, PivotableFilter::class) && $allowedFilter->isPivot()) {
            if (!($relation instanceof BelongsToMany)) {
                throw new DeniedFilterException($pendingFilter);
            }

            $parentModelFqcn = get_class($relation->getParent());

            if (!$allowedFilter->getPivotParentModelFqcns()->containsStrict($parentModelFqcn)) {
                throw new DeniedFilterException($pendingFilter);
            }
        }

        $filterBuilder = new FilterBuilder(
            $pendingFilter,
            new EloquentContext(
                $pendingFilter->model(),
                $relation,
                is_a($allowedFilter, PivotableFilter::class) && $allowedFilter->isPivot(),
            ),
        );

        return $filterBuilder
            ->target($target)
            ->build();
    }
}
