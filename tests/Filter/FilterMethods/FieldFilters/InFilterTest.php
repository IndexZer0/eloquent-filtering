<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $in filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$in',
                'value'  => [
                    'George Raymond Richard Martin',
                    'J. R. R. Tolkien',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', ['$in']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" in ('George Raymond Richard Martin', 'J. R. R. Tolkien')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('can perform $in filter with :null modifier', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$in:null',
                'value'  => [
                    'George Raymond Richard Martin',
                    null,
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [
                FilterType::IN->withModifiers('null'),
            ]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where ("authors"."name" in ('George Raymond Richard Martin') or "authors"."name" is null)
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});
