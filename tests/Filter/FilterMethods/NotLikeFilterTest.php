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

it('can perform $notLike filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notLike',
                'value'  => 'red',
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$notLike']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" NOT LIKE '%red%'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
    ->and($models->first()->name)->toBe('James');

});
