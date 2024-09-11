<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\UnsupportedModifierException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Types\Types;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('defaults to have all supported modifiers allowed', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:end',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});

it('can have only specific modifiers allowed | valid modifier', function (): void {
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
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

});

it('can have only specific modifiers allowed | invalid modifier', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:start',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE->withModifiers('end')]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['George Raymond Richard Martin']);

})->throws(DeniedFilterException::class, '"$like:start" filter for "name" is not allowed');

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
        ),
    );

})->throws(DeniedFilterException::class, '"$like:end" filter for "name" is not allowed');

it('exceptions when unsupported modifier specified', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:end',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE->withModifiers('unsupported-modifier')]),
        ),
    );

})->throws(UnsupportedModifierException::class, '"unsupported-modifier" is not a supported modifier');

it('exceptions when using AllTypesAllowed with invalid modifier', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like:this-modifier-does-not-exist',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', Types::all()),
        ),
    );

})->throws(DeniedFilterException::class, '"$like:this-modifier-does-not-exist" filter for "name" is not allowed');
