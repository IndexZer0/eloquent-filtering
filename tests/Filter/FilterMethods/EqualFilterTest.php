<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
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
        Filter::allow(
            Filter::column('name', ['$eq']),
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
    mixed $value,
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
                'value'  => $value,
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$eq']),
        )
    );

    expect($query->toRawSql())->toBe($expected_sql);

})->with([
    'string' => [
        'value'            => 'string',
        'expected_sql'     => 'select * from "authors" where "name" = \'string\'',
        'expect_exception' => false,
    ],
    'int' => [
        'value'            => 420,
        'expected_sql'     => 'select * from "authors" where "name" = 420',
        'expect_exception' => false,
    ],
    'float' => [
        'value'            => 69.420,
        'expected_sql'     => 'select * from "authors" where "name" = 69.42',
        'expect_exception' => false,
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
