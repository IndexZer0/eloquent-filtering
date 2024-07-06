<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

class FilterParser implements FilterParserContract
{
    protected FilterCollection $filterCollection;

    protected AllowedFilterList $allowedFilterList;

    protected AvailableFilters $availableFilters;

    public function __construct()
    {
        $this->filterCollection = new FilterCollection();
        $this->availableFilters = resolve(AvailableFilters::class);
    }

    public function parse(array $filters, AllowedFilterList $allowedFilterList): FilterCollection
    {
        $this->allowedFilterList = $allowedFilterList;

        foreach ($filters as $filter) {
            Suppression::honour(function () use ($filter): void {
                $filterMethod = $this->parseFilter($filter);
                $this->filterCollection->push($filterMethod);
            });
        }

        return $this->filterCollection;
    }

    private function parseFilter(mixed $filter): FilterMethod
    {
        $requestedFilter = $this->parseFilterType($filter);
        $filterFqcn = $this->findFilterMethodFqcn($requestedFilter->type);

        $validatedData = $this->validateFilterFormat($filter, $filterFqcn);

        $approvedFilter = $this->allowedFilterList->ensureAllowed(
            new PendingFilter($requestedFilter, $filterFqcn, $validatedData)
        );

        return $approvedFilter->createFilter();
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

    private function validateFilterFormat(array $filter, string $filterFqcn): array
    {
        try {
            /** @var FilterMethod $filterFqcn */
            return Validator::validate($filter, $filterFqcn::format());
        } catch (ValidationException $ve) {
            throw new MalformedFilterFormatException($filterFqcn::type(), $ve);
        }
    }
}
