<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $or and $and filter', function (): void {
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
                        'type'  => '$and',
                        'value' => [
                            [
                                'target' => 'name',
                                'type'   => '$eq',
                                'value'  => 'J. R. R. Tolkien',
                            ],
                            [
                                'target' => 'books',
                                'type'   => '$has',
                                'value'  => [
                                    [
                                        'target' => 'title',
                                        'type'   => '$eq',
                                        'value'  => 'The Lord of the Rings',
                                    ],
                                ],
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
                allowedFilters: Filter::only(
                    Filter::field('title', [FilterType::EQUAL]),
                )
            )
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where (("authors"."name" = 'George Raymond Richard Martin') or ((("authors"."name" = 'J. R. R. Tolkien') and (exists (select * from "books" where "authors"."id" = "books"."author_id" and "books"."title" = 'The Lord of the Rings')))))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
