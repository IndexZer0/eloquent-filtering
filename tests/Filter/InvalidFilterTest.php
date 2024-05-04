<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

$dataSets = [
    'not array' => [
        'filter' => 1,
    ],
    'type does not exist' => [
        'filter' => [
            'invalid-type-key' => '$null',
        ],
    ],
    'type is not string' => [
        'filter' => [
            'type' => 1,
        ],
    ],
];

it('throws exception when filter is invalid | not suppressed', function (mixed $filter): void {

    Author::filter(
        [
            $filter,
        ],
        Filter::all()
    );

})
    ->with($dataSets)
    ->throws(InvalidFilterException::class, 'Filter must be an array containing `type` (string).');

it('does not throw exception when filter is invalid | suppressed', function (mixed $filter): void {

    $this->setSuppression("filter.invalid", true);

    $query = Author::filter(
        [
            $filter,
        ],
        Filter::all()
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

})->with($dataSets);
