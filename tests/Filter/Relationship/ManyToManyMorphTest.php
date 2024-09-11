<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Epic;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Issue;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Label;

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

    $pivotFilter = Filter::field('labeled_by', [FilterType::EQUAL])
        ->pivot(Epic::class, Issue::class);

    $epicQuery = Epic::filter(
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
                $pivotFilter
            )),
        )
    );

    $expectedEpicQuerySql = <<< SQL
        select * from "epics" where exists (select * from "labels" inner join "labelables" on "labels"."id" = "labelables"."label_id" where "epics"."id" = "labelables"."labelable_id" and "labelables"."labelable_type" = 'epics' and "labels"."name" = 'label-1' and "labelables"."labeled_by" = 'user-1')
        SQL;

    expect($epicQuery->toRawSql())->toBe($expectedEpicQuerySql);

    $epicModels = $epicQuery->get();

    expect($epicModels->count())->toBe(1)
        ->and($epicModels->first()->name)->toBe('epic-1');

    $issueQuery = Issue::filter(
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
                        'value'  => 'user-2',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('labels', [FilterType::HAS], Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                $pivotFilter
            )),
        )
    );

    $expectedIssueQuerySql = <<< SQL
        select * from "issues" where exists (select * from "labels" inner join "labelables" on "labels"."id" = "labelables"."label_id" where "issues"."id" = "labelables"."labelable_id" and "labelables"."labelable_type" = 'issues' and "labels"."name" = 'label-1' and "labelables"."labeled_by" = 'user-2')
        SQL;

    expect($issueQuery->toRawSql())->toBe($expectedIssueQuerySql);

    $issueModels = $issueQuery->get();

    expect($issueModels->count())->toBe(1)
        ->and($issueModels->first()->name)->toBe('issue-1');

    $labelQuery = Label::filter(
        [
            [
                'type'  => '$or',
                'value' => [
                    [
                        'target' => 'epics',
                        'type'   => '$has',
                        'value'  => [
                            [
                                'target' => 'labeled_by',
                                'type'   => '$eq',
                                'value'  => 'user-1',
                            ],
                        ],
                    ],
                    [
                        'target' => 'issues',
                        'type'   => '$has',
                        'value'  => [
                            [
                                'target' => 'labeled_by',
                                'type'   => '$eq',
                                'value'  => 'user-4',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation('epics', [FilterType::HAS], Filter::only(
                Filter::field('labeled_by', [FilterType::EQUAL])->pivot(Label::class)
            )),
            Filter::relation('issues', [FilterType::HAS], Filter::only(
                Filter::field('labeled_by', [FilterType::EQUAL])->pivot(Label::class)
            )),
        )
    );

    $expectedLabelQuerySql = <<< SQL
        select * from "labels" where ((exists (select * from "epics" inner join "labelables" on "epics"."id" = "labelables"."labelable_id" where "labels"."id" = "labelables"."label_id" and "labelables"."labelable_type" = 'epics' and "labelables"."labeled_by" = 'user-1')) or (exists (select * from "issues" inner join "labelables" on "issues"."id" = "labelables"."labelable_id" where "labels"."id" = "labelables"."label_id" and "labelables"."labelable_type" = 'issues' and "labelables"."labeled_by" = 'user-4')))
        SQL;

    expect($labelQuery->toRawSql())->toBe($expectedLabelQuerySql);

    $labelModels = $labelQuery->get();

    expect($labelModels->count())->toBe(2)
        ->and($labelModels->pluck('name')->toArray())->toBe([
            'label-1', 'label-2',
        ]);

});
