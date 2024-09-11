<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFiltersPayloadException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

class FilterParser implements FilterParserContract
{
    protected FilterCollection $filterCollection;

    protected Model $model;
    protected ?Relation $relation;
    protected ?PendingFilter $previousPendingFilter;

    protected AllowedFilterList $allowedFilterList;

    protected AvailableFilters $availableFilters;

    public function __construct()
    {
        $this->filterCollection = new FilterCollection();
        $this->availableFilters = resolve(AvailableFilters::class);
    }

    public function parse(
        Model $model,
        array $filters,
        AllowedFilterList $allowedFilterList,
        ?Relation $relation = null,
        ?PendingFilter $previousPendingFilter = null
    ): FilterCollection {
        $this->model = $model;
        $this->relation = $relation;
        $this->allowedFilterList = $allowedFilterList;
        $this->previousPendingFilter = $previousPendingFilter;

        if (!array_is_list($filters)) {
            throw new InvalidFiltersPayloadException('Filters must be an array list.');
        }

        foreach ($filters as $index => $filter) {
            Suppression::honour(function () use ($index, $filter): void {
                $this->filterCollection->push(
                    $this->parseFilter($index, $filter)
                );
            });
        }

        return $this->filterCollection;
    }

    private function parseFilter(int $index, mixed $filter): FilterMethod
    {
        $requestedFilter = $this->parseFilterType($filter);
        $filterFqcn = $this->findFilterMethodFqcn($requestedFilter->type);

        $pendingFilter = new PendingFilter(
            $requestedFilter,
            $filterFqcn,
            $filter,
            $this->model,
            $this->relation,
            $this->previousPendingFilter,
            $index
        );

        $pendingFilter->validate();

        return $this->allowedFilterList->ensureAllowed($pendingFilter);
    }

    private function parseFilterType(mixed $filter): RequestedFilter
    {
        if (!is_array($filter) || !array_key_exists('type', $filter) || !is_string($filter['type'])) {
            InvalidFilterException::throw();
        }

        return RequestedFilter::fromString($filter['type']);
    }

    private function findFilterMethodFqcn(string $type): string
    {
        return $this->availableFilters->find($type);
    }
}
