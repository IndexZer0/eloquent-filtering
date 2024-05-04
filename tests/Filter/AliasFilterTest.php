<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('can alias field | field filter', function (): void {

    $query = Author::filter(
        [
            [
                'type'   => '$eq',
                'target' => 'name',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field(Target::alias('name', 'name_alias'), ['$eq'])
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name_alias" = 'George Raymond Richard Martin'
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
            Filter::relation(Target::alias('documents', 'books'), ['$has'])
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

});
