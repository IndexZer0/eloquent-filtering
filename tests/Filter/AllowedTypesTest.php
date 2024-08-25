<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Types\Types;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can have all types allowed', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$like',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$in',
                'value'  => [
                    'George Raymond Richard Martin',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', Types::all()),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin' and "authors"."name" LIKE '%George Raymond Richard Martin%' and "authors"."name" in ('George Raymond Richard Martin')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});

it('can have only some types allowed', function (): void {

    $this->setSuppression("filter.denied", true);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$like',
                'value'  => 'George Raymond Richard Martin',
            ],
            [
                'target' => 'name',
                'type'   => '$in',
                'value'  => [
                    'George Raymond Richard Martin',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', Types::only(['$eq', '$like'])),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin' and "authors"."name" LIKE '%George Raymond Richard Martin%'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});
