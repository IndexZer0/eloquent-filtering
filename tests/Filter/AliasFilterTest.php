<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

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

it('can alias field | Filter::all()', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$eq',
                'target' => 'name',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::all(
            Target::alias('name', 'name_alias')
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name_alias" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});

it('can alias relation | Filter::all()', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$has',
                'target' => 'documents',
                'value'  => [],
            ],
        ],
        Filter::all(
            Target::alias('documents', 'books')
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
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
