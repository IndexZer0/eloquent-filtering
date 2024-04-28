<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

class FilterParser implements FilterParserContract
{
    protected FilterCollection $filterCollection;

    protected FilterableList $filterableList;

    public function __construct()
    {
        $this->filterCollection = new FilterCollection();
    }

    public function parse(array $filters, FilterableList $filterableList): FilterCollection
    {
        $this->filterableList = $filterableList;

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
        $filterType = $this->ensureFilterHasType($filter);
        $filterFqcn = $this->findFilterMethodFqcn($filterType);

        $validatedData = $this->validateFilterFormat($filter, $filterFqcn);

        $filterableList = $this->filterableList->ensureAllowed($filterType, data_get($validatedData, 'target'));

        if (is_a($filterFqcn, HasChildFilters::class, true)) {

            /** @var FilterParser $filterParser */
            $filterParser = resolve(FilterParserContract::class);
            $validatedData['value'] = $filterParser->parse($validatedData['value'], $filterableList);

        }

        return new $filterFqcn(...$validatedData);
    }

    private function ensureFilterHasType(mixed $filter): string
    {
        if (!is_array($filter) || !array_key_exists('type', $filter) || !is_string($filter['type'])) {
            InvalidFilterException::throw();
        }

        return $filter['type'];
    }

    private function findFilterMethodFqcn(string $type): string
    {
        /** @var AvailableFilters $availableFilters */
        $availableFilters = resolve(AvailableFilters::class);
        return $availableFilters->find($type);
    }

    private function validateFilterFormat(array $filter, string $filterFqcn): array
    {
        try {
            /** @var FilterMethod $filterFqcn */
            $validator = Validator::make($filter, $filterFqcn::format());
            return $validator->safe()->all();
        } catch (ValidationException $ve) {
            throw new MalformedFilterFormatException($filterFqcn::type(), $ve);
        }
    }
}
