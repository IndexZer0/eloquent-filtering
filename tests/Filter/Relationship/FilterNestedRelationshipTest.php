<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter by nested relationships when allowed', function (): void {

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
                            Filter::field('content', [FilterType::EQUAL]),
                        ),
                    ),
                ),
            ),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "books"."title" = 'A Game of Thrones' and (("books"."description" LIKE '%A Game of Thrones%') or ("books"."description" LIKE '%Song of Ice and Fire%')) and exists (select * from "comments" where "books"."id" = "comments"."book_id" and "comments"."content" = 'Thanks D&D :S'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('can not filter by nested relationships when no filter list supplied', function (): void {

    Author::filter(
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
                                'value'  => 'Thanks D&D :S',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    );

})->throws(DeniedFilterException::class, '"$eq" filter for "name" is not allowed');

it('can not filter by nested relationship when not explicitly allowed | not suppressed', function (): void {

    Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [
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
            Filter::relation('books', [FilterType::HAS]),
        ),
    );

})->throws(DeniedFilterException::class, '"$has" filter for "comments" is not allowed');

it('can not filter by nested relationship when not explicitly allowed | suppressed', function (): void {

    $this->setSuppression('filter.denied', true);

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
                                'value'  => 'Thanks D&D :S',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('books', [FilterType::HAS]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('honours the allowed filter list all the way down the nested relation chain | suppressed', function (): void {

    $this->setSuppression('filter.denied', true);

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
                                'value'  => 'Thanks D&D :S',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation(
                'books',
                [FilterType::HAS],
                Filter::only(
                    Filter::relation(
                        'comments',
                        [FilterType::HAS],
                    ),
                ),
            ),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id" and exists (select * from "comments" where "books"."id" = "comments"."book_id"))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
