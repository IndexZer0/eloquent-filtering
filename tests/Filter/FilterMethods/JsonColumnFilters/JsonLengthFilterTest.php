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
        Filter::allowOnly(
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
    mixed $value,
    ?string $expected_sql,
    bool $expect_exception
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
                'value'    => $value,
            ],
        ],
        Filter::allowOnly(
            Filter::jsonColumn('data->array', ['$jsonLength']),
        )
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    'int' => [
        'value'            => 420,
        'expected_sql'     => 'select * from "api_responses" where json_array_length("data", \'$."array"\') = 420',
        'expect_exception' => false,
    ],
    'string' => [
        'value'            => 'string',
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'float' => [
        'value'            => 69.420,
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'null' => [
        'value'            => null,
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'bool' => [
        'value'            => true,
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'empty array' => [
        'value'            => [],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'non empty array' => [
        'value'            => [1],
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
    'object' => [
        'value'            => new stdClass(),
        'expected_sql'     => null,
        'expect_exception' => true,
    ],
]);
