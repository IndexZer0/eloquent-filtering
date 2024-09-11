<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\File;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\Subscription;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Show;

beforeEach(function (): void {
    $this->createMorphRecordsForIncludeRelationFields();
    $this->createMorphRecordsForWithoutMorphMapAndIncludeRelationFields();
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

    $query = File::filter([
        [
            'target' => 'fileable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'contracts',
                    'value' => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'contract-1',
                        ],
                    ],
                ],
                [
                    'type'  => 'accounts',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'account-1',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $expectedSql = <<< SQL
        select * from "files" where (("files"."fileable_type" = 'contracts' and exists (select * from "contracts" where "files"."fileable_id" = "contracts"."id" and "contracts"."title" = 'contract-1')) or ("files"."fileable_type" = 'accounts' and exists (select * from "accounts" where "files"."fileable_id" = "accounts"."id" and "accounts"."name" = 'account-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4);

});

it('can include morph relation models allowed fields | no morph map', function (): void {

    $query = Subscription::filter([
        [
            'target' => 'subscribable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'food_delivery_services',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'food-delivery-service-1',
                        ],
                    ],
                ],
                [
                    'type'  => 'sasses',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'sass-1',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $expectedSql = <<< SQL
        select * from "subscriptions" where (("subscriptions"."subscribable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\FoodDeliveryService' and exists (select * from "food_delivery_services" where "subscriptions"."subscribable_id" = "food_delivery_services"."id" and "food_delivery_services"."name" = 'food-delivery-service-1')) or ("subscriptions"."subscribable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\Sass' and exists (select * from "sasses" where "subscriptions"."subscribable_id" = "sasses"."id" and "sasses"."name" = 'sass-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4);

});
