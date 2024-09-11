<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ApiResponse;

beforeEach(function (): void {
    $this->createApiResponses();
});

it('can perform $jsonLength filter', function (): void {
    $query = ApiResponse::filter(
        [
            [
                'target'   => 'data->array',
                'type'     => '$jsonLength',
                'operator' => '=',
                'value'    => 4,
            ],
        ],
        Filter::only(
            Filter::field('data->array', [FilterType::JSON_LENGTH]),
        ),
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where json_array_length("api_responses"."data", '$."array"') = 4
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1', 'Api 2']);

});

it('only accepts int for value', function (
    array   $value_container,
    ?string $expected_sql,
    bool    $expect_exception,
    ?string $expected_exception_message,
    ?array $expected_errors,
): void {

    try {
        $query = ApiResponse::filter(
            [
                [
                    'target'   => 'data->array',
                    'type'     => '$jsonLength',
                    'operator' => '=',
                    ...$value_container,
                ],
            ],
            Filter::only(
                Filter::field('data->array', [FilterType::JSON_LENGTH]),
            ),
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
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'string' => [
        'value_container'            => ['value' => 'string', ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],
    'numeric_string' => [
        'value_container'            => ['value' => '1', ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],
    'float' => [
        'value_container'            => ['value' => 420.69, ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],
    'null' => [
        'value_container'            => ['value' => null, ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'bool' => [
        'value_container'            => ['value' => true, ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],
    'empty array' => [
        'value_container'            => ['value' => [], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value field is required.',
            ],
        ],
    ],
    'non empty array' => [
        'value_container'            => ['value' => [1], ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],
    'object' => [
        'value_container'            => ['value' => new stdClass(), ],
        'expected_sql'               => null,
        'expect_exception'           => true,
        'expected_exception_message' => 'Data->array filter does not match required format. (and 1 more error)',
        'expected_errors'            => [
            'data->array' => [
                'Data->array filter does not match required format.',
            ],
            'data->array.value' => [
                'The value must be integer',
            ],
        ],
    ],

    // Success Cases
    'int' => [
        'value_container'            => ['value' => 420, ],
        'expected_sql'               => 'select * from "api_responses" where json_array_length("api_responses"."data", \'$."array"\') = 420',
        'expect_exception'           => false,
        'expected_exception_message' => null,
        'expected_errors'            => null,
    ],
]);
