<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Fred',
    ]);
    Author::create([
        'id'   => 2,
        'name' => 'Fred2',
    ]);
    Author::create([
        'id'   => 3,
        'name' => 'James',
    ]);
});

it('can perform $like:start filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:start',
                'value'  => 'Fred',
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$like:start']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" LIKE 'Fred%'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
