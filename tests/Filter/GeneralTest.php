<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFiltersPayloadException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws InvalidFiltersPayloadException when providing a non list array', function (): void {
    Author::filter(
        [
            'string_key' => [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
        )
    );

})->throws(InvalidFiltersPayloadException::class, 'Filters must be an array list.');
