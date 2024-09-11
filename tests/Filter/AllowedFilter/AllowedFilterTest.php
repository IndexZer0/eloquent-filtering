<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use Laravel\SerializableClosure\SerializableClosure;

it('does not allow filters that are not for same context', function (
    array $filter,
    SerializableClosure $allowed_filter,
    string $expected_exception_message
): void {

    $this->expectException(DeniedFilterException::class);
    $this->expectExceptionMessage($expected_exception_message);

    Author::filter(
        [
            $filter,
        ],
        Filter::only(
            $allowed_filter->getClosure()()
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
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::relation('name', [FilterType::EQUAL])
        ),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],
    'field filter | morph relation allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::morphRelation('name', [FilterType::EQUAL])
        ),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],
    'field filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::custom('$eq')
        ),
        'expected_exception_message' => '"$eq" filter for "name" is not allowed',
    ],

    // Relation filter
    'relation filter | field allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::field('books', [FilterType::HAS])
        ),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],
    'relation filter | morph relation allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::morphRelation('books', [FilterType::HAS])
        ),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],
    'relation filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'books',
            'value'  => [],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::custom('$has')
        ),
        'expected_exception_message' => '"$has" filter for "books" is not allowed',
    ],

    // Morph Relation filter
    'morph relation filter | field allowed filter' => [
        'filter' => [
            'type'   => '$hasMorph',
            'target' => 'books',
            'types'  => [
                [
                    'type' => '*',
                ],
            ],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::field('books', [FilterType::HAS_MORPH])
        ),
        'expected_exception_message' => '"$hasMorph" filter for "books" is not allowed',
    ],
    'morph relation filter | relation allowed filter' => [
        'filter' => [
            'type'   => '$hasMorph',
            'target' => 'books',
            'types'  => [
                [
                    'type' => '*',
                ],
            ],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::relation('books', [FilterType::HAS_MORPH])
        ),
        'expected_exception_message' => '"$hasMorph" filter for "books" is not allowed',
    ],
    'morph relation filter | custom allowed filter' => [
        'filter' => [
            'type'   => '$hasMorph',
            'target' => 'books',
            'types'  => [
                [
                    'type' => '*',
                ],
            ],
        ],
        'allowed_filter' => new SerializableClosure(
            fn () => Filter::custom('$hasMorph')
        ),
        'expected_exception_message' => '"$hasMorph" filter for "books" is not allowed',
    ],
]);
