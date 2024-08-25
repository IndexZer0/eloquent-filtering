<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
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
            Filter::field('name', ['$eq']),
        )
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
            Filter::relation('books', ['$has', '$doesntHas']),
        )
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
        Filter::none()
    );

})->throws(DeniedFilterException::class, "\"\$or\" filter is not allowed");

it('must have at least two child filters', function (
    array $value_container,
    ?string $expected_sql,
    bool    $expect_exception
): void {

    if ($expect_exception) {
        $this->expectException(MalformedFilterFormatException::class);
        $this->expectExceptionMessage('"$or" filter does not match required format.');
    }

    $query = Author::filter(
        [
            [
                'type' => '$or',
                ...$value_container,
            ],
        ],
        Filter::all()
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    'no value' => [
        'value_container'  => [],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'value not array' => [
        'value_container'  => ['value' => true],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'value empty array' => [
        'value_container'  => ['value' => []],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'value only one element' => [
        'value_container' => ['value' => [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ]],
        'expected_sql'     => null,
        'expect_exception' => true,
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
        'expected_sql'     => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') or ("authors"."name" = \'J. R. R. Tolkien\'))',
        'expect_exception' => false,
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
        'expected_sql'     => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') or ("authors"."name" = \'J. R. R. Tolkien\') or ("authors"."name" = \'J. K. Rowling\'))',
        'expect_exception' => false,
    ],

]);
