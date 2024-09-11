<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ModelIsFilterable;

beforeEach(function (): void {
    $this->createAuthors();
});

it('doesnt resolveRelationsAllowedFields when relation method does not exist', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'nonExistingRelationship',
                'type'   => '$has',
                'value'  => [],
            ],
        ],
        Filter::only(
            Filter::relation(
                'nonExistingRelationship',
                [FilterType::HAS],
            )->includeRelationFields(),
        ),
    );

})->throws(BadMethodCallException::class);

it('doesnt includeRelationFields when relation model is not filterable', function (): void {

    $query = ModelIsFilterable::filter(
        [
            [
                'target' => 'notFilterable',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'name',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation(
                'notFilterable',
                [FilterType::HAS],
            )->includeRelationFields(),
        ),
    );

})->throws(DeniedFilterException::class, '"$eq" filter for "name" is not allowed');
