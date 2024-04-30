<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('uses default allowed list from config', function (
    string $default,
    bool   $expect_exception,
): void {

    config()->set('eloquent-filtering.default_allowed_list', $default);

    if ($expect_exception) {
        $this->expectException(DeniedFilterException::class);
        $this->expectExceptionMessage('"$eq" filter for "name" is not allowed');
    }

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

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
