<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Book;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $and filter on base model', function (): void {
    $query = Book::filter(
        [
            [
                'type'  => '$and',
                'value' => [
                    [
                        'target' => 'title',
                        'type'   => '$eq',
                        'value'  => 'A Game of Thrones',
                    ],
                    [
                        'target' => 'description',
                        'type'   => '$like',
                        'value'  => 'A Game of Thrones',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::field('title', ['$eq']),
            Filter::field('description', ['$like']),
        )
    );

    $expectedSql = <<< SQL
        select * from "books" where (("books"."title" = 'A Game of Thrones') and ("books"."description" LIKE '%A Game of Thrones%'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('can perform $and filter | multiple exists', function (): void {
    $query = Author::filter(
        [
            [
                'type'  => '$and',
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
        select * from "authors" where ((exists (select * from "books" where "authors"."id" = "books"."author_id")) and (not exists (select * from "books" where "authors"."id" = "books"."author_id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});

it('has the correct DeniedFilterException message', function (): void {
    Author::filter(
        [
            [
                'type'  => '$and',
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

})->throws(DeniedFilterException::class, "\"\$and\" filter is not allowed");

it('must have at least two child filters', function (
    array $value_container,
    ?string $expected_sql,
    bool    $expect_exception
): void {

    if ($expect_exception) {
        $this->expectException(MalformedFilterFormatException::class);
        $this->expectExceptionMessage('"$and" filter does not match required format.');
    }

    $query = Author::filter(
        [
            [
                'type' => '$and',
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
        'expected_sql'     => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') and ("authors"."name" = \'J. R. R. Tolkien\'))',
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
        'expected_sql'     => 'select * from "authors" where (("authors"."name" = \'George Raymond Richard Martin\') and ("authors"."name" = \'J. R. R. Tolkien\') and ("authors"."name" = \'J. K. Rowling\'))',
        'expect_exception' => false,
    ],

]);
