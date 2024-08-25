<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $gte filter', function (): void {
    $query = AuthorProfile::filter(
        [
            [
                'target' => 'age',
                'type'   => '$gte',
                'value'  => 20,
            ],
        ],
        Filter::only(
            Filter::field('age', [FilterType::GREATER_THAN_EQUAL_TO]),
        )
    );

    $expectedSql = <<< SQL
        select * from "author_profiles" where "author_profiles"."age" >= 20
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
    ->and($models->pluck('id')->toArray())->toBe([1, 2]);

});
