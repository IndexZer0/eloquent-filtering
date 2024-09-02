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
    bool $expect_exception,
    ?string $expected_exception_message,
    ?array $expected_errors
): void {

    try {

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

        if ($expect_exception) {
            $this->fail('Should have thrown an exception');
        }

        expect($query->toRawSql())->toBe($expected_sql);

    } catch (MalformedFilterFormatException $mffe) {
        if (!$expect_exception) {
            $this->fail('Should not have thrown an exception');
        }

        expect($mffe->getMessage())->toBe($expected_exception_message)
            ->and($mffe->errors())->toBe($expected_errors);

    }


})->with([
    // Failing Cases
    'no value' => [
        'value_container'            => [],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'empty array' => [
        'value_container'            => ['value' => [[]], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value.0' => [
                'The value.0 must be string, integer, float or null.',
            ],
        ],
    ],
    'array containing bool' => [
        'value_container'            => ['value' => [true], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value.0' => [
                'The value.0 must be string, integer, float or null.',
            ],
        ],
    ],
    'array containing object' => [
        'value_container'            => ['value' => [new stdClass()], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value.0' => [
                'The value.0 must be string, integer, float or null.',
            ],
        ],
    ],

    // Success Cases
    'array containing null' => [
        'value_container'            => ['value' => [null], ],
        'expected_sql'               => 'select * from "authors" where (0 = 1 or "authors"."name" is null)',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'array containing int' => [
        'value_container'            => ['value' => [420]],
        'expected_sql'               => 'select * from "authors" where "authors"."name" in (420)',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'array containing string' => [
        'value_container'            => ['value' => ['string'], ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" in (\'string\')',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'array containing numeric_string' => [
        'value_container'            => ['value' => ['1'], ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" in (\'1\')',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'array containing float' => [
        'value_container'            => ['value' => [420.69], ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" in (420.69)',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
]);
