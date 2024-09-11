<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;

beforeEach(function (): void {
    $this->createMorphRecords();
});

it('can perform $doesntHasMorph filter', function (): void {
    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$doesntHasMorph',
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
            [FilterType::DOESNT_HAS_MORPH],
            Filter::morphType('*'),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and not exists (select * from "articles" where "images"."imageable_id" = "articles"."id")) or ("images"."imageable_type" = 'user_profiles' and not exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
