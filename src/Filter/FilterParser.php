<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

readonly class FilterParser implements FilterParserContract
{
    public FilterCollection $filterCollection;

    public function __construct()
    {
        $this->filterCollection = new FilterCollection();
    }

    public function parse(array $filters): FilterCollection
    {
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
        $filterType = $this->ensureFilterIsValid($filter);
        $filterClass = $this->findFilterMethodClass($filterType);

        $validatedData = $this->validateFilterFormat($filter, $filterClass);

        return new $filterClass(...$validatedData);
    }

    private function ensureFilterIsValid(mixed $filter): string
    {
        if (!is_array($filter) || !array_key_exists('type', $filter) || !is_string($filter['type'])) {
            InvalidFilterException::throw();
        }

        return $filter['type'];
    }

    private function findFilterMethodClass(string $type): string
    {
        /** @var AvailableFilters $availableFilters */
        $availableFilters = resolve(AvailableFilters::class);
        return $availableFilters->find($type);
    }

    private function validateFilterFormat(array $filter, string $filterClass): array
    {
        try {
            $validator = Validator::make($filter, $filterClass::format());
            return $validator->safe()->all();
        } catch (ValidationException $ve) {
            MalformedFilterFormatException::throw($filterClass, $ve);
        }
    }
}
