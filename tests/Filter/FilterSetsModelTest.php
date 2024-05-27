<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\FilterSet\Environment;

beforeEach(function (): void {

});

it('can use filter sets', function (): void {

    $query = Environment::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'Production',
            ],
            [
                'target' => 'iam_user',
                'type'   => '$eq',
                'value'  => 'AWS User',
            ],
            [
                'target' => 'secrets',
                'type'   => '$has',
                'value'  => [
                    [
                        'target' => 'name',
                        'type'   => '$eq',
                        'value'  => 'SOME_ENVIRONMENT_SECRET',
                    ],
                    [
                        'target' => 'value',
                        'type'   => '$like',
                        'value'  => 'secret',
                    ],
                ],
            ],
        ],
        'admin',
    );

    $expectedSql = <<< SQL
        select * from "environments" where "name" = 'Production' and "iam_user" = 'AWS User' and exists (select * from "secrets" where "environments"."id" = "secrets"."environment_id" and "name" = 'SOME_ENVIRONMENT_SECRET' and "value" LIKE '%secret%')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(0);

});
