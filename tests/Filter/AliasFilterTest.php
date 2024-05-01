<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('uses alias when specified', function (
    array          $filter,
    FilterableList $allowed_filters,
    string         $expected_sql
): void {

    $query = Author::filter(
        [
            $filter,
        ],
        $allowed_filters
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    'column filter' => [
        'filter' => [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'George Raymond Richard Martin',
        ],
        'allowed_filters' => Filter::only(
            Filter::column(Target::alias('name', 'name_alias'), ['$eq'])
        ),
        'expected_sql' => 'select * from "authors" where "name_alias" = \'George Raymond Richard Martin\'',
    ],
    'relation filter' => [
        'filter' => [
            'type'   => '$has',
            'target' => 'documents',
            'value'  => [],
        ],
        'allowed_filters' => Filter::only(
            Filter::relation(Target::alias('documents', 'books'), ['$has'])
        ),
        'expected_sql' => 'select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")',
    ],
]);
