<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws exception when filter format is invalid | not suppressed', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => 'value should be boolean',
            ],
        ],
        Filter::all()
    );

})->throws(MalformedFilterFormatException::class, '"$null" filter does not match required format.');

it('does not throw exception when filter format is invalid | suppressed', function (): void {

    $this->setSuppression("filter.malformed_format", true);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$null',
                'value'  => 'value should be boolean',
            ],
        ],
        Filter::all()
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
