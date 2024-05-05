<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Sort\Exceptions\MalformedSortFormatException;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws exception when sort format is invalid | not suppressed', function (): void {

    Author::sort(
        [
            [
                'target' => 'name',
            ],
        ],
        Sort::all()
    );

})->throws(MalformedSortFormatException::class, 'a sort does not match required format.');

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
