<?php

declare(strict_types=1);

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
        Filter::allowOnly(
            Filter::column('name', ['$eq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where (("name" = 'George Raymond Richard Martin') or ("name" = 'J. R. R. Tolkien'))
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
        Filter::allowOnly(
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
