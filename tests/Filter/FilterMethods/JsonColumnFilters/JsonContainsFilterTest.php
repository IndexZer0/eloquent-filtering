<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
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
        Filter::allowOnly(
            Filter::jsonColumn('data->array', ['$jsonContains']),
        )
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where exists (select 1 from json_each("data", '$."array"') where "json_each"."value" is 'own-array-value-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1']);

});
