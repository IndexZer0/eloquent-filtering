<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\ImageTwo;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Show;

beforeEach(function (): void {

});

it('can include relation models allowed fields', function (): void {

    $query = Show::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Book Of Mormon',
            ],
            [
                'target' => 'e',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'starting_at',
                        'type'   => '$between',
                        'value'  => [
                            '2024-01-01',
                            '2024-02-01',
                        ],
                    ],
                    [
                        'target' => 'tickets',
                        'type'   => '$has',
                        'value'  => [
                            [
                                'target' => 'type',
                                'type'   => '$eq',
                                'value'  => 'Premium',
                            ],
                        ],
                    ],

                ],
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "shows" where "shows"."name" = 'Book Of Mormon' and exists (select * from "events" where "shows"."id" = "events"."show_id" and "events"."starting_at" between '2024-01-01' and '2024-02-01' and exists (select * from "tickets" where "events"."id" = "tickets"."event_id" and "tickets"."type" = 'Premium'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});

it('can include morph relation models allowed fields', function (): void {

    $query = ImageTwo::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'article_twos',
                    'value' => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'article-1',
                        ],
                    ],
                ],
                [
                    'type'  => 'user_profile_twos',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'user-profile-1',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $expectedSql = <<< SQL
        select * from "image_twos" where (("image_twos"."imageable_type" = 'article_twos' and exists (select * from "article_twos" where "image_twos"."imageable_id" = "article_twos"."id" and "article_twos"."title" = 'article-1')) or ("image_twos"."imageable_type" = 'user_profile_twos' and exists (select * from "user_profile_twos" where "image_twos"."imageable_id" = "user_profile_twos"."id" and "user_profile_twos"."name" = 'user-profile-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
