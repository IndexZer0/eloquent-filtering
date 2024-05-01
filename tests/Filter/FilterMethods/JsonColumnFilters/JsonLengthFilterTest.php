<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
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
            Filter::jsonColumn('data->array', ['$jsonLength']),
        )
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where json_array_length("data", '$."array"') = 4
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1', 'Api 2']);

});

it('only accepts int for value', function (
    array   $value_container,
    ?string $expected_sql,
    bool    $expect_exception
): void {

    if ($expect_exception) {
        $this->expectException(MalformedFilterFormatException::class);
        $this->expectExceptionMessage('"$jsonLength" filter does not match required format.');
    }

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
            Filter::jsonColumn('data->array', ['$jsonLength']),
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
    'string' => [
        'value_container'  => ['value' => 'string', ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'numeric_string' => [
        'value_container'  => ['value' => '1', ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'float' => [
        'value_container'  => ['value' => 420.69, ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'null' => [
        'value_container'  => ['value' => null, ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'bool' => [
        'value_container'  => ['value' => true, ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'empty array' => [
        'value_container'  => ['value' => [], ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'non empty array' => [
        'value_container'  => ['value' => [1], ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'object' => [
        'value_container'  => ['value' => new stdClass(), ],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],

    // Success Cases
    'int' => [
        'value_container'  => ['value' => 420, ],
        'expected_sql'     => 'select * from "api_responses" where json_array_length("data", \'$."array"\') = 420',
        'expect_exception' => false,
    ],
]);
