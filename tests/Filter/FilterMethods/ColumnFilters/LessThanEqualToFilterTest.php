<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $lte filter', function (): void {
    $query = AuthorProfile::filter(
        [
            [
                'target' => 'age',
                'type'   => '$lte',
                'value'  => 30,
            ],
        ],
        Filter::allowOnly(
            Filter::column('age', ['$lte']),
        )
    );

    $expectedSql = <<< SQL
        select * from "author_profiles" where "age" <= 30
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
    ->and($models->pluck('id')->toArray())->toBe([1, 2]);

});
