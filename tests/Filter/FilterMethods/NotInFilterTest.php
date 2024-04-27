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

it('can perform $notIn filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notIn',
                'value'  => [
                    'Freddy',
                    'Frederickson',
                ],
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$notIn']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" not in ('Freddy', 'Frederickson')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
