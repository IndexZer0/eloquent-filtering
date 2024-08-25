<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Post;

it('can alias field', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$eq',
                'target' => 'name',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field(Target::alias('name', 'name_alias'), [FilterType::EQUAL])
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name_alias" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias relation', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$has',
                'target' => 'documents',
                'value'  => [],
            ],
        ],
        Filter::only(
            Filter::relation(Target::alias('documents', 'books'), [FilterType::HAS])
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias relations field', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$eq',
                'target' => 'target_from_request_1',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'type'   => '$has',
                'target' => 'target_from_request_2',
                'value'  => [
                    [
                        'type'   => '$eq',
                        'target' => 'target_from_request_1',
                        'value'  => 'Game Of Thrones',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::field(Target::alias('target_from_request_1', 'name'), [FilterType::EQUAL]),
            Filter::relation(
                Target::alias('target_from_request_2', 'books'),
                [FilterType::HAS],
                allowedFilters: Filter::only(
                    Filter::field(Target::alias('target_from_request_1', 'title'), [FilterType::EQUAL]),
                )
            ),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "books"."title" = 'Game Of Thrones')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias morph relation | morphRelation filter', function (): void {

    $query = Image::filter(
        [
            [
                'target' => 'image',
                'type'   => '$hasMorph',
                'types'  => [
                    [
                        'type' => 'articles',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::morphRelation(
                Target::alias('image', 'imageable'),
                [FilterType::HAS_MORPH],
                Filter::morphType('articles')
            )
        )
    );

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias morph relation field | morphRelation filter', function (): void {

    $query = Image::filter(
        [
            [
                'target' => 'image',
                'type'   => '$hasMorph',
                'types'  => [
                    [
                        'type'  => 'articles',
                        'value' => [
                            [
                                'type'   => '$eq',
                                'target' => 'article_title',
                                'value'  => 'article-1',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::morphRelation(
                Target::alias('image', 'imageable'),
                [FilterType::HAS_MORPH],
                Filter::morphType('articles', Filter::only(
                    Filter::field(Target::alias('article_title', 'title'), [FilterType::EQUAL])
                ))
            )
        )
    );

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id" and "articles"."title" = 'article-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias pivot | Filter::all()', function (): void {

    $query = Post::filter([
        [
            'type'   => '$eq',
            'target' => 'target_from_request_1',
            'value'  => 'post-title-1',
        ],
        [
            'type'   => '$has',
            'target' => 'target_from_request_2',
            'value'  => [
                [
                    'type'   => '$eq',
                    'target' => 'target_from_request_1',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field(Target::alias('target_from_request_1', 'title'), [FilterType::EQUAL]),
        Filter::relation(
            Target::alias('target_from_request_2', 'tags'),
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field(Target::alias('target_from_request_1', 'tagged_by'), [FilterType::EQUAL])->pivot(),
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "posts" where "posts"."title" = 'post-title-1' and exists (select * from "tags" inner join "post_tag" on "tags"."id" = "post_tag"."tag_id" where "posts"."id" = "post_tag"."post_id" and "post_tag"."tagged_by" = 'tagged-by-user-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});
