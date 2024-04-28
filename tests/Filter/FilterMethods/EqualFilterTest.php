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

it('does not accept non scalar values', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => true,
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$eq']),
        )
    );

})->throws(MalformedFilterFormatException::class, '"$eq" filter does not match required format.');
