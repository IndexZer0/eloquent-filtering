<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('does not allow filters that are not for same context', function (
    array $filter,
    AllowedFilter $allowed_filter,
    string $expected_exception_message
): void {

    $this->expectException(DeniedFilterException::class);
    $this->expectExceptionMessage($expected_exception_message);

    Author::filter(
        [
            $filter,
        ],
        Filter::only(
            $allowed_filter
        )
    );

})->with([
    // Field filter
    'field filter | relation allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter'             => Filter::relation('name', ['$eq']),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],
    'field filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter'             => Filter::custom(['$eq']),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],

    // relation filter
    'relation filter | field allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter'             => Filter::field('books', ['$has']),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],
    'relation filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter'             => Filter::custom(['$has']),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],
]);
