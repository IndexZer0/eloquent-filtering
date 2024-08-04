<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Sort\Exceptions\MalformedSortFormatException;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws exception when sort is not array | not suppressed', function (): void {

    Author::sort(
        [
            'string',
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'Sort must be an array.');

it('throws exception when sort value is missing | not suppressed', function (): void {

    Author::sort(
        [
            [
                'target' => 'name',
            ],
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'The value field is required.');

it('throws exception when sort value is invalid | not suppressed', function (): void {

    Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'invalid',
            ],
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'The value must be one of the following types: asc, desc');

it('throws exception when sort target is missing | not suppressed', function (): void {

    Author::sort(
        [
            [
                'value' => 'asc',
            ],
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'The target field is required.');

it('throws exception when sort target is invalid | not suppressed', function (): void {

    Author::sort(
        [
            [
                'target' => ['array'],
                'value'  => 'asc',
            ],
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'The target field must be a string.');

it('does not throw exception when sort format is invalid | suppressed', function (): void {

    $this->setSuppression("sort.malformed_format", true);

    $query = Author::sort(
        [
            [
                'target' => 'name',
            ],
        ],
        Sort::all()
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
