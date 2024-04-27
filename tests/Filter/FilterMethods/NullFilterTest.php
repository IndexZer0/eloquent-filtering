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
        'name' => null,
    ]);
});

it('can perform $null filter | null', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => true,
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$null']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" is null
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(2);

});

it('can perform $null filter | not null', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => false,
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$null']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" is not null
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});
