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
});

it('can perform $notEq filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notEq',
                'value'  => 'Fred2',
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$notEq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" != 'Fred2'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});
