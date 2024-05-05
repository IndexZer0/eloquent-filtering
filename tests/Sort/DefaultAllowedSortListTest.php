<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('uses default allowed sort list from config', function (
    string $default,
    bool   $expect_exception,
): void {

    config()->set('eloquent-filtering.default_allowed_sort_list', $default);

    if ($expect_exception) {
        $this->expectException(DeniedSortException::class);
        $this->expectExceptionMessage('"name" sort is not allowed');
    }

    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'asc',
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name" asc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('id')->toArray())->toBe([1,2]);

})->with([
    'none' => [
        'default'          => 'none',
        'expect_exception' => true,
    ],
    'all' => [
        'default'          => 'all',
        'expect_exception' => false,
    ],
]);
