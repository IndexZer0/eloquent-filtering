<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Invoice;

beforeEach(function (): void {
    $this->createMorphRecordsForWithoutMorphMap();
});

it('can filter by all morphs', function (): void {

    $query = Invoice::filter([
        [
            'target' => 'invoiceable',
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
            'invoiceable',
            [FilterType::HAS_MORPH],
            Filter::morphType('*'),
        )
    ));

    $expectedSql = <<< SQL
        select * from "invoices" where (("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and exists (select * from "businesses" where "invoices"."invoiceable_id" = "businesses"."id")) or ("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client' and exists (select * from "clients" where "invoices"."invoiceable_id" = "clients"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(8)
        ->and($models->pluck('amount')->toArray())->toBe([
            1, 2, 3, 4, 5, 6, 7, 8,
        ]);
});

it('can filter by all morphs with child filters', function (): void {

    $query = Invoice::filter([
        [
            'target' => 'invoiceable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [
                        [
                            'target' => 'created_at',
                            'type'   => '$lte',
                            'value'  => '4000-01-01',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'invoiceable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                '*',
                Filter::only(
                    Filter::field('created_at', [FilterType::LESS_THAN_EQUAL_TO])
                )
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "invoices" where (("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and exists (select * from "businesses" where "invoices"."invoiceable_id" = "businesses"."id" and "businesses"."created_at" <= '4000-01-01')) or ("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client' and exists (select * from "clients" where "invoices"."invoiceable_id" = "clients"."id" and "clients"."created_at" <= '4000-01-01')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(8)
        ->and($models->pluck('amount')->toArray())->toBe([
            1, 2, 3, 4, 5, 6, 7, 8,
        ]);
});

it('can filter by specific morphs', function (): void {

    $query = Invoice::filter([
        [
            'target' => 'invoiceable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'invoiceable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                Business::class
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "invoices" where (("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and exists (select * from "businesses" where "invoices"."invoiceable_id" = "businesses"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4)
        ->and($models->pluck('amount')->toArray())->toBe([
            1, 2, 3, 4,
        ]);
});

it('can filter by specific morphs with child filters', function (): void {

    $query = Invoice::filter([
        [
            'target' => 'invoiceable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'business-1',
                        ],
                    ],
                ],
                [
                    'type'  => 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'client-1',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'invoiceable',
            [FilterType::HAS_MORPH],
            // TODO maybe this.
            /*Filter::morphTypes([
                'articles' => Filter::only(
                    Filter::field('title', [FilterType::EQUAL]),
                ),
                'user_profiles' => Filter::only(
                    Filter::field('name', [FilterType::EQUAL])
                )
            ]),*/
            Filter::morphType(Business::class, Filter::only(
                Filter::field('name', [FilterType::EQUAL])
            )),
            Filter::morphType(Client::class, Filter::only(
                Filter::field('name', [FilterType::EQUAL])
            ))
        )
    ));

    $expectedSql = <<< SQL
        select * from "invoices" where (("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and exists (select * from "businesses" where "invoices"."invoiceable_id" = "businesses"."id" and "businesses"."name" = 'business-1')) or ("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client' and exists (select * from "clients" where "invoices"."invoiceable_id" = "clients"."id" and "clients"."name" = 'client-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4)
        ->and($models->pluck('amount')->toArray())->toBe([
            1, 2, 5, 6,
        ]);
});

it('can filter by nested relation in specific morph', function (): void {

    $query = Invoice::filter([
        [
            'target' => 'invoiceable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'business-1',
                        ],
                        [
                            'target' => 'invoices',
                            'type'   => '$has',
                            'value'  => [
                                [
                                    'target' => 'amount',
                                    'type'   => '$eq',
                                    'value'  => 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'invoiceable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                Business::class,
                Filter::only(
                    Filter::field('name', [FilterType::EQUAL]),
                    Filter::relation(
                        'invoices',
                        [FilterType::HAS],
                        Filter::only(
                            Filter::field('amount', [FilterType::EQUAL])
                        )
                    ),
                )
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "invoices" where (("invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and exists (select * from "businesses" where "invoices"."invoiceable_id" = "businesses"."id" and "businesses"."name" = 'business-1' and exists (select * from "invoices" where "businesses"."id" = "invoices"."invoiceable_id" and "invoices"."invoiceable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business' and "invoices"."amount" = 1))))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('amount')->toArray())->toBe([
            1, 2,
        ]);
});
