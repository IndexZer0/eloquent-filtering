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
        'name' => 'Frederick',
    ]);
});

it('can perform $in filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$in',
                'value'  => [
                    'Fred',
                    'Frederick',
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$in']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" in ('Fred', 'Frederick')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
