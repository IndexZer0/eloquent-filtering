<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;

beforeEach(function (): void {
    $this->createMorphRecords();
});

it('can perform $hasMorph filter', function (): void {
    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType('*'),
        )
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id")) or ("images"."imageable_type" = 'user_profiles' and exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(8)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
            'image-3',
            'image-4',
            'image-5',
            'image-6',
            'image-7',
            'image-8',
        ]);

});
