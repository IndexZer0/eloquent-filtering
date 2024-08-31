<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
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
            Filter::field('name', [FilterType::IN]),
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

it('only accepts string, int, float, null for value', function (
    array $value_container,
    ?string $expected_sql,
    bool $expect_exception
): void {

    if ($expect_exception) {
        $this->expectException(MalformedFilterFormatException::class);
        $this->expectExceptionMessage('"$in:null" filter does not match required format.');
    }

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$in:null',
                ...$value_container,
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::IN]),
        )
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    // Failing Cases
    'no value' => [
        'value_container'  => [],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'bool' => [
        'value_container'  => ['value' => [true], ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'empty array' => [
        'value_container'  => ['value' => [[]], ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'object' => [
        'value_container'  => ['value' => [new stdClass()], ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],

    // Success Cases
    'null' => [
        'value_container'  => ['value' => [null], ],
        'expected_sql'     => 'select * from "authors" where (0 = 1 or "authors"."name" is null)',
        'expect_exception' => false,
    ],
    'int' => [
        'value_container'  => ['value' => [420]],
        'expected_sql'     => 'select * from "authors" where "authors"."name" in (420)',
        'expect_exception' => false,
    ],
    'string' => [
        'value_container'  => ['value' => ['string'], ],
        'expected_sql'     => 'select * from "authors" where "authors"."name" in (\'string\')',
        'expect_exception' => false,
    ],
    'numeric_string' => [
        'value_container'  => ['value' => ['1'], ],
        'expected_sql'     => 'select * from "authors" where "authors"."name" in (\'1\')',
        'expect_exception' => false,
    ],
    'float' => [
        'value_container'  => ['value' => [420.69], ],
        'expected_sql'     => 'select * from "authors" where "authors"."name" in (420.69)',
        'expect_exception' => false,
    ],
]);
