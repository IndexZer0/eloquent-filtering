<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ApiResponse;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
    $this->createApiResponses();
});

it('can perform $eq filter', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::EQUAL]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('only accepts string, int, float for value', function (
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
                    'type'   => '$eq',
                    ...$value_container,
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
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
    'null' => [
        'value_container'            => ['value' => null, ],
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
    'bool' => [
        'value_container'            => ['value' => true, ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value' => [
                'The value must be string, integer or float.',
            ],
        ],
    ],
    'empty array' => [
        'value_container'            => ['value' => [], ],
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
    'non empty array' => [
        'value_container'            => ['value' => [1], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value' => [
                'The value must be string, integer or float.',
            ],
        ],
    ],
    'object' => [
        'value_container'            => ['value' => new stdClass(), ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Name filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'name' => [
                'Name filter does not match required format.',
            ],
            'name.value' => [
                'The value must be string, integer or float.',
            ],
        ],
    ],

    // Success Cases
    'int' => [
        'value_container'            => ['value' => 420],
        'expected_sql'               => 'select * from "authors" where "authors"."name" = 420',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'string' => [
        'value_container'            => ['value' => 'string', ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" = \'string\'',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'numeric_string' => [
        'value_container'            => ['value' => '1', ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" = \'1\'',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
    'float' => [
        'value_container'            => ['value' => 420.69, ],
        'expected_sql'               => 'select * from "authors" where "authors"."name" = 420.69',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
]);

it('can perform $eq filter on json field', function (): void {
    $query = ApiResponse::filter(
        [
            [
                'target' => 'data->own-key-1',
                'type'   => '$eq',
                'value'  => 'own-value-1',
            ],
        ],
        Filter::only(
            Filter::field('data->own-key-1', [FilterType::EQUAL]),
        )
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where json_extract("api_responses"."data", '$."own-key-1"') = 'own-value-1'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1']);

});
