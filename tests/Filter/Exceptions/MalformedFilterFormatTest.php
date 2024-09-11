<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Filter\FilterType;

it('throws exception when filter format is invalid | not suppressed', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => 'value should be boolean',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::NULL]),
        )
    );

})->throws(MalformedFilterFormatException::class, 'Name filter does not match required format. (and 1 more error)');

it('does not throw exception when filter format is invalid | suppressed', function (): void {

    $this->setSuppression("filter.malformed_format", true);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => 'value should be boolean',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::NULL]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});

it('has correct nested validation error keys', function (): void {

    try {
        $query = Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'George Raymond Richard Martin',
                ],
                [
                    'target' => 'books',
                    'type'   => '$has',
                    'value'  => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'A Game of Thrones',
                        ],
                        [
                            'type'  => '$or',
                            'value' => [
                                [
                                    'target' => 'description',
                                    'type'   => '$like',
                                    'value'  => 'A Game of Thrones',
                                ],
                                [
                                    'target' => 'description',
                                    'type'   => '$like',
                                    'value'  => 'Song of Ice and Fire',
                                ],
                            ],
                        ],
                        [
                            'type'   => '$has',
                            'target' => 'comments',
                            'value'  => [
                                [
                                    'target' => 'content',
                                    'type'   => '$eq',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::EQUAL]),
                        Filter::field('description', [FilterType::LIKE]),
                        Filter::relation(
                            'comments',
                            [FilterType::HAS],
                            Filter::only(
                                Filter::field('content', [FilterType::EQUAL])
                            )
                        )
                    )
                )
            )
        );
    } catch (MalformedFilterFormatException $mffe) {
        expect($mffe->getMessage())->toBe('Content filter does not match required format. (and 1 more error)')
            ->and($mffe->errors())->toBe([
                'books.comments.content' => [
                    'Content filter does not match required format.',
                ],
                'books.comments.content.value' => [
                    'The value field is required.',
                ],
            ]);
    }

});


it('has correct nested validation error keys with $or', function (): void {

    try {
        $query = Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'George Raymond Richard Martin',
                ],
                [
                    'target' => 'books',
                    'type'   => '$has',
                    'value'  => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'A Game of Thrones',
                        ],
                        [
                            'type'  => '$or',
                            'value' => [
                                [
                                    'target' => 'description',
                                    'type'   => '$like',
                                ],
                                [
                                    'target' => 'description',
                                    'type'   => '$like',
                                    'value'  => 'Song of Ice and Fire',
                                ],
                            ],
                        ],
                        [
                            'type'   => '$has',
                            'target' => 'comments',
                            'value'  => [
                                [
                                    'target' => 'content',
                                    'type'   => '$eq',
                                    'value'  => 'Thanks D&D :S',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::EQUAL]),
                        Filter::field('description', [FilterType::LIKE]),
                        Filter::relation(
                            'comments',
                            [FilterType::HAS],
                            Filter::only(
                                Filter::field('content', [FilterType::EQUAL])
                            )
                        )
                    )
                )
            )
        );
    } catch (MalformedFilterFormatException $mffe) {
        expect($mffe->getMessage())->toBe('Description filter does not match required format. (and 1 more error)')
            ->and($mffe->errors())->toBe([
                'books.$or.1.description' => [
                    'Description filter does not match required format.',
                ],
                'books.$or.1.description.value' => [
                    'The value field is required.',
                ],
            ]);
    }

});
