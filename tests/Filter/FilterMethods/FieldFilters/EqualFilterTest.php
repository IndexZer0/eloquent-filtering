<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
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
            Filter::field('name', ['$eq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1);

});

it('only accepts string, int, float for value', function (
    array $value_container,
    ?string $expected_sql,
    bool $expect_exception
): void {

    if ($expect_exception) {
        $this->expectException(MalformedFilterFormatException::class);
        $this->expectExceptionMessage('"$eq" filter does not match required format.');
    }

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                ...$value_container,
            ],
        ],
        Filter::only(
            Filter::field('name', ['$eq']),
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
        'value_container'  => ['value' => 420],
        'expected_sql'     => 'select * from "authors" where "name" = 420',
        'expect_exception' => false,
    ],
    'string' => [
        'value_container'  => ['value' => 'string', ],
        'expected_sql'     => 'select * from "authors" where "name" = \'string\'',
        'expect_exception' => false,
    ],
    'numeric_string' => [
        'value_container'  => ['value' => '1', ],
        'expected_sql'     => 'select * from "authors" where "name" = \'1\'',
        'expect_exception' => false,
    ],
    'float' => [
        'value_container'  => ['value' => 420.69, ],
        'expected_sql'     => 'select * from "authors" where "name" = 420.69',
        'expect_exception' => false,
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
            Filter::field('data->own-key-1', ['$eq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "api_responses" where json_extract("data", '$."own-key-1"') = 'own-value-1'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->pluck('name')->toArray())->toBe(['Api 1']);

});
