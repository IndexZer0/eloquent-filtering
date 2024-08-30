<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Epic;

beforeEach(function (): void {
    $this->createManyToManyMorphRecords();
});

it('can perform $has filter on many to many morph', function (): void {
    $query = Epic::filter(
        [
            [
                'target' => 'labels',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'label-1',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('labels', [FilterType::HAS], Filter::only(
                Filter::field('name', [FilterType::EQUAL])
            )),
        )
    );

    $expectedSql = <<< SQL
        select * from "epics" where exists (select * from "labels" inner join "labelables" on "labels"."id" = "labelables"."label_id" where "epics"."id" = "labelables"."labelable_id" and "labelables"."labelable_type" = 'epics' and "labels"."name" = 'label-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->name)->toBe('epic-1');

});

it('can perform pivot filter on many to many morph', function (): void {
    $query = Epic::filter(
        [
            [
                'target' => 'labels',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'label-1',
                    ],
                    [
                        'target' => 'labeled_by',
                        'type'   => '$eq',
                        'value'  => 'user-1',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('labels', [FilterType::HAS], Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('labeled_by', [FilterType::EQUAL])->pivot('labelables')
            )),
        )
    );

    $expectedSql = <<< SQL
        select * from "epics" where exists (select * from "labels" inner join "labelables" on "labels"."id" = "labelables"."label_id" where "epics"."id" = "labelables"."labelable_id" and "labelables"."labelable_type" = 'epics' and "labels"."name" = 'label-1' and "labelables"."labeled_by" = 'user-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->name)->toBe('epic-1');

});
