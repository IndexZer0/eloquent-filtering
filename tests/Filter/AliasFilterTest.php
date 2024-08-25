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

it('can alias relation | relation filter', function (): void {

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

it('can alias relations field | Filter::all()', function (): void {

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
        Filter::all(
            Target::alias('target_from_request_1', 'name'),
            Target::relationAlias(
                'target_from_request_2',
                'books',
                Target::alias('target_from_request_1', 'title')
            ),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin' and exists (select * from "books" where "authors"."id" = "books"."author_id" and "title" = 'Game Of Thrones')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});
