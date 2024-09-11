<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $or filter on base model', function (): void {
    $query = Author::filter(
        [
            [
                'type'  => '$or',
                'value' => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'George Raymond Richard Martin',
                    ],
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'J. R. R. Tolkien',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where (("authors"."name" = 'George Raymond Richard Martin') or ("authors"."name" = 'J. R. R. Tolkien'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('can perform $or filter | multiple exists', function (): void {
    $query = Author::filter(
        [
            [
                'type'  => '$or',
                'value' => [
                    [
                        'target' => 'books',
                        'type'   => '$has',
                        'value'  => [],
                    ],
                    [
                        'target' => 'books',
                        'type'   => '$doesntHas',
                        'value'  => [],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('books', [FilterType::HAS, FilterType::DOESNT_HAS]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where ((exists (select * from "books" where "authors"."id" = "books"."author_id")) or (not exists (select * from "books" where "authors"."id" = "books"."author_id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('has the correct DeniedFilterException message', function (): void {
    Author::filter(
        [
            [
                'type'  => '$or',
                'value' => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'George Raymond Richard Martin',
                    ],
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'J. R. R. Tolkien',
                    ],
                ],
            ],
        ],
        Filter::none(),
    );

})->throws(DeniedFilterException::class, '"$or" filter is not allowed');

it('must have at least two child filters', function (
    array $value_container,
    ?string $expected_sql,
    bool    $expect_exception,
    ?string $expected_exception_message,
    ?array $expected_errors,
): void {

    try {
        $query = Author::filter(
            [
                [
                    'type' => '$or',
                    ...$value_container,
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
            ),
        );

        if ($expect_exception) {
            $this->fail('Should have thrown an exception');
        }

        expect($query->toRawSql())->toBe($expected_sql);

    } catch (MalformedFilterFormatException $mffe) {
        if (!$expect_exception) {
            $this->fail('Should not have thrown an exception');
        }

        expect($mffe->getMessage())->toBe($expected_exception_message)
            ->and($mffe->errors())->toBe($expected_errors);
    }

})->with([
    'no value' => [
        'value_container'            => [],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => '$or.0 filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            '$or.0' => [
                '$or.0 filter does not match required format.',
            ],
            '$or.0.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'value not array' => [
        'value_container'            => ['value' => true],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => '$or.0 filter does not match required format. (and 2 more errors)',
        'expected_errors'            => [
            '$or.0' => [
                '$or.0 filter does not match required format.',
            ],
            '$or.0.value' => [
                'The value field must be an array.',
                'The value field must have at least 2 items.',
            ],
        ],
    ],
    'value empty array' => [
        'value_container'            => ['value' => []],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => '$or.0 filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            '$or.0' => [
                '$or.0 filter does not match required format.',
            ],
            '$or.0.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'value only one element' => [
        'value_container' => ['value' => [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ]],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => '$or.0 filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            '$or.0' => [
                '$or.0 filter does not match required format.',
            ],
            '$or.0.value' => [
                'The value field must have at least 2 items.',
            ],
        ],
    ],
    'value two elements' => [
        'value_container' => ['value' => [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'J. R. R. Tolkien',
            ],
        ]],
        'expected_sql'               => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') or ("authors"."name" = \'J. R. R. Tolkien\'))',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'value three elements' => [
        'value_container' => ['value' => [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'J. R. R. Tolkien',
            ],
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'J. K. Rowling',
            ],
        ]],
        'expected_sql'               => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') or ("authors"."name" = \'J. R. R. Tolkien\') or ("authors"."name" = \'J. K. Rowling\'))',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],

]);
