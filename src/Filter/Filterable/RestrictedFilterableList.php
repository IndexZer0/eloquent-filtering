<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableDefinition;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class RestrictedFilterableList implements FilterableList
{
    private Collection $list;

    public function __construct(FilterableDefinition ...$filterableDefinitions)
    {
        $this->list = collect($filterableDefinitions)->keyBy(
            fn (FilterableDefinition $filterableDefinition) => $filterableDefinition->target()
        );
    }

    public function ensureAllowed(FilterMethod $filter): RestrictedFilterableList
    {
        if (!$filter->hasTarget()) {
            // Always allow filters without a specific target to be allowed.
            // These are filters such as `$and`, and '$or'.
            return $this;
        }

        if ($filter instanceof TargetedFilterMethod) {
            foreach ($this->list as $filterableDefinition) {
                if ($filterableDefinition->target() === $filter->target() &&
                    in_array($filter->type(), $filterableDefinition->types())
                ) {
                    if ($filterableDefinition instanceof FilterableRelation) {
                        return new self(...$filterableDefinition->filterableDefinitions);
                    }
                    return new self();
                }
            }
        }

        DeniedFilterException::throw($filter);
    }

    public function all(): array
    {
        return $this->list->all();
    }

    public function getColumns(): array
    {

    }
}
