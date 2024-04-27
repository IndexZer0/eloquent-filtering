<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Book;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Comment;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Fred',
    ]);
    Book::create([
        'id'          => 1,
        'author_id'   => 1,
        'title'       => 'title',
        'description' => 'description',
    ]);
    Comment::create([
        'book_id' => 1,
        'content' => 'This is a comment',
    ]);

    Author::create([
        'id'   => 2,
        'name' => 'Fred',
    ]);
    Book::create([
        'id'          => 2,
        'author_id'   => 2,
        'title'       => 'title',
        'description' => 'description',
    ]);
    Comment::create([
        'book_id' => 2,
        'content' => 'This is a another comment',
    ]);
});

it('filters by nested relationships when allowed', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Fred',
            ],
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'title',
                        'type'   => '$eq',
                        'value'  => 'title',
                    ],
                    [
                        'type'  => '$or',
                        'value' => [
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description',
                            ],
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description 2',
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
                                'value'  => 'This is a comment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$eq']),
            Filter::relation(
                'books',
                ['$has'],
                Filter::column('title', ['$eq']),
                Filter::column('description', ['$eq']),
                Filter::relation(
                    'comments',
                    ['$has'],
                    Filter::column('content', ['$eq'])
                )
            )
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'Fred' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "title" = 'title' and (("description" = 'description') or ("description" = 'description 2')) and exists (select * from "comments" where "books"."id" = "comments"."book_id" and "content" = 'This is a comment'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('filters by nested relationships when no filter list supplied', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Fred',
            ],
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'title',
                        'type'   => '$eq',
                        'value'  => 'title',
                    ],
                    [
                        'type'  => '$or',
                        'value' => [
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description',
                            ],
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description 2',
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
                                'value'  => 'This is a comment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'Fred' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "title" = 'title' and (("description" = 'description') or ("description" = 'description 2')) and exists (select * from "comments" where "books"."id" = "comments"."book_id" and "content" = 'This is a comment'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('filters by nested relationships with "Filter::all()"', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Fred',
            ],
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'title',
                        'type'   => '$eq',
                        'value'  => 'title',
                    ],
                    [
                        'type'  => '$or',
                        'value' => [
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description',
                            ],
                            [
                                'target' => 'description',
                                'type'   => '$eq',
                                'value'  => 'description 2',
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
                                'value'  => 'This is a comment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'Fred' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "title" = 'title' and (("description" = 'description') or ("description" = 'description 2')) and exists (select * from "comments" where "books"."id" = "comments"."book_id" and "content" = 'This is a comment'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

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
                                'value'  => 'This is a comment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::allow(
            Filter::relation('books', ['$has'])
        ),
    );

})->throws(DeniedFilterException::class, '"$has" filter for "comments" is not allowed');

it('can not filter by nested relationship when not explicitly allowed | suppressed', function (): void {

    config()->set('eloquent-filtering.suppress.filter.denied', true);

    $query = Author::filter(
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
                                'value'  => 'This is a comment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::allow(
            Filter::relation('books', ['$has'])
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
