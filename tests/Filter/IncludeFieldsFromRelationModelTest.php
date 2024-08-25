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
        select * from "shows" where "name" = 'Book Of Mormon' and exists (select * from "events" where "shows"."id" = "events"."show_id" and "starting_at" between '2024-01-01' and '2024-02-01' and exists (select * from "tickets" where "events"."id" = "tickets"."event_id" and "type" = 'Premium'))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
