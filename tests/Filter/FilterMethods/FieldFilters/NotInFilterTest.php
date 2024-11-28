<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $notIn filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notIn',
                'value'  => [
                    'J. K. Rowling',
                    'William Shakespeare',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::NOT_IN]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" not in ('J. K. Rowling', 'William Shakespeare')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('can perform $notIn filter with :null modifier', function (): void {

    Author::create(['name' => null]);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notIn:null',
                'value'  => [
                    'George Raymond Richard Martin',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [
                FilterType::NOT_IN->withModifiers('null'),
            ]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where ("authors"."name" not in ('George Raymond Richard Martin') and "authors"."name" is not null)
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['J. R. R. Tolkien']);

});

it('can perform $notIn filter with :null modifier in combination with other filter.', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$notEq',
                'value'  => 'William Shakespeare',
            ],
            [
                'target' => 'name',
                'type'   => '$notIn:null',
                'value'  => [
                    'George Raymond Richard Martin',
                ],
            ],
        ],
        Filter::only(
            Filter::field('name', [
                FilterType::NOT_IN->withModifiers('null'),
                FilterType::NOT_EQUAL,
            ]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" != 'William Shakespeare' and ("authors"."name" not in ('George Raymond Richard Martin') and "authors"."name" is not null)
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['J. R. R. Tolkien']);

});
