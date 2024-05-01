<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('does not allow filters that are not for same usage', function (
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
    // column filter
    'column filter | relation allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter'             => Filter::relation('name', ['$eq']),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],
    'column filter | jsonColumn allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter'             => Filter::jsonColumn('name', ['$eq']),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],
    'column filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter'             => Filter::custom(['$eq']),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],

    // relation filter
    'relation filter | column allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter'             => Filter::column('books', ['$has']),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],
    'relation filter | jsonColumn allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter'             => Filter::jsonColumn('books', ['$has']),
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

    // jsonColumn filter
    'jsonColumn filter | column allowed filter' => [
        'filter' => [
            'type'   => '$jsonContains',
            'target' => 'data->books',
            'value'  => 'A Game of Thrones',
        ],
        'allowed_filter'             => Filter::column('data->books', ['$jsonContains']),
        'expected_exception_message' => '"$jsonContains" filter for "data->books" is not allowed',
    ],
    'jsonColumn filter | relation allowed filter' => [
        'filter' => [
            'type'   => '$jsonContains',
            'target' => 'data->books',
            'value'  => 'A Game of Thrones',
        ],
        'allowed_filter'             => Filter::relation('data->books', ['$jsonContains']),
        'expected_exception_message' => '"$jsonContains" filter for "data->books" is not allowed',
    ],
    'jsonColumn filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$jsonContains',
            'target' => 'data->books',
            'value'  => 'A Game of Thrones',
        ],
        'allowed_filter'             => Filter::custom(['$jsonContains']),
        'expected_exception_message' => '"$jsonContains" filter for "data->books" is not allowed',
    ],
]);
