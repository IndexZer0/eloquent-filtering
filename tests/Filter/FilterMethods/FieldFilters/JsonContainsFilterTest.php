<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ApiResponse;

beforeEach(function (): void {
    $this->createApiResponses();
});

it('can perform $jsonContains filter', function (): void {
    $query = ApiResponse::filter(
        [
            [
                'target' => 'data->array',
                'type'   => '$jsonContains',
                'value'  => 'own-array-value-1',
            ],
        ],
        Filter::only(
            Filter::field('data->array', [FilterType::JSON_CONTAINS]),
        )
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where exists (select 1 from json_each("api_responses"."data", '$."array"') where "json_each"."value" is 'own-array-value-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1']);

});

it('supports various wildcard and non wildcard targets', function (
    string  $allowed_target,
    string  $requested_target,
    ?string $expected_sql,
    ?string $expected_exception_message
): void {

    if ($expected_exception_message !== null) {
        $this->expectException(DeniedFilterException::class);
        $this->expectExceptionMessage($expected_exception_message);
    }

    $query = ApiResponse::filter(
        [
            [
                'target' => $requested_target,
                'type'   => '$jsonContains',
                'value'  => 'own-array-value-1',
            ],
        ],
        Filter::only(
            Filter::field($allowed_target, [FilterType::JSON_CONTAINS]),
        )
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    'target is database field' => [
        'allowed_target'             => 'data',
        'requested_target'           => 'data',
        'expected_sql'               => 'select * from "api_responses" where exists (select 1 from json_each("api_responses"."data") where "json_each"."value" is \'own-array-value-1\')',
        'expected_exception_message' => null,
    ],
    'invalid json path (ends with "->")' => [
        'allowed_target'             => 'data->*',
        'requested_target'           => 'data->something->',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->something->" is not allowed',
    ],
    'single wildcard | does match' => [
        'allowed_target'             => 'data->*->array',
        'requested_target'           => 'data->something->array',
        'expected_sql'               => 'select * from "api_responses" where exists (select 1 from json_each("api_responses"."data", \'$."something"."array"\') where "json_each"."value" is \'own-array-value-1\')',
        'expected_exception_message' => null,
    ],
    'single wildcard | does not match' => [
        'allowed_target'             => 'data->*->array',
        'requested_target'           => 'data->something->arra',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->something->arra" is not allowed',
    ],
    'no wildcard | matches' => [
        'allowed_target'             => 'data->something->array',
        'requested_target'           => 'data->something->array',
        'expected_sql'               => 'select * from "api_responses" where exists (select 1 from json_each("api_responses"."data", \'$."something"."array"\') where "json_each"."value" is \'own-array-value-1\')',
        'expected_exception_message' => null,
    ],
    'no wildcard | does not match' => [
        'allowed_target'             => 'data->something->array',
        'requested_target'           => 'data->something->arra',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->something->arra" is not allowed',
    ],
    'multiple wildcards | does match' => [
        'allowed_target'             => 'data->*->*->array',
        'requested_target'           => 'data->something->sub->array',
        'expected_sql'               => 'select * from "api_responses" where exists (select 1 from json_each("api_responses"."data", \'$."something"."sub"."array"\') where "json_each"."value" is \'own-array-value-1\')',
        'expected_exception_message' => null,
    ],
    'multiple wildcards | does not match' => [
        'allowed_target'             => 'data->*->*->array',
        'requested_target'           => 'data->something->array',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->something->array',
    ],
    'amount of segments differ | does not match' => [
        'allowed_target'             => 'data->*->array',
        'requested_target'           => 'data->something->array->array',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->something->array->array" is not allowed',
    ],
    'requested target has wildcard' => [
        'allowed_target'             => 'data->something->array',
        'requested_target'           => 'data->*->array',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->*->array" is not allowed',
    ],
    'can have int where wildcard is' => [
        'allowed_target'             => 'data->*->response->*->values',
        'requested_target'           => 'data->0->response->5->values',
        'expected_sql'               => 'select * from "api_responses" where exists (select 1 from json_each("api_responses"."data", \'$."0"."response"."5"."values"\') where "json_each"."value" is \'own-array-value-1\')',
        'expected_exception_message' => null,
    ],
    'cant have "->" as wildcard' => [
        'allowed_target'             => 'data->*->response->*->values',
        'requested_target'           => 'data->->->response->->->values',
        'expected_sql'               => null,
        'expected_exception_message' => '"$jsonContains" filter for "data->->->response->->->values" is not allowed',
    ],
    'cant have non alphanumeric characters and underscores as wildcard' => [
        'allowed_target'             => 'data->*->response',
        'requested_target'           => 'data->$->response',
        'expected_sql'               => null,
        'expected_exception_message' => '$jsonContains" filter for "data->$->response" is not allowed',
    ],
]);
