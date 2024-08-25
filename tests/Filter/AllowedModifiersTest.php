<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can have all modifiers allowed', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:end',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', ['$like']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});

it('can have some modifiers allowed', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:end',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE->withModifiers('end')]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});

it('can have zero modifiers allowed', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:end',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE->withoutModifiers()]),
        )
    );

})->throws(DeniedFilterException::class, '"$like:end" filter for "name" is not allowed');
