<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can perform $between filter', function (): void {
    $query = AuthorProfile::filter(
        [
            [
                'target' => 'age',
                'type'   => '$between',
                'value'  => [
                    19,
                    21,
                ],
            ],
        ],
        Filter::only(
            Filter::field('age', [FilterType::BETWEEN]),
        )
    );

    $expectedSql = <<< SQL
        select * from "author_profiles" where "author_profiles"."age" between 19 and 21
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
    ->and($models->first()->id)->toBe(1);

});
