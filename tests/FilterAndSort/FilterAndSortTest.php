<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Name',
    ]);
    Author::create([
        'id'   => 2,
        'name' => 'Fred 1',
    ]);
    Author::create([
        'id'   => 3,
        'name' => 'Fred 2',
    ]);
});

it('can filter and sort together', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like',
                'value'  => 'Fred',
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$like']),
        ),
    )->sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::allow(
            Sort::column('name')
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" LIKE '%Fred%' order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('id')->toArray())->toBe([3, 2]);

});
