<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Comment;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Person;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Project;

it('EqualFilter | $eq', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$eq',
            'target' => 'name',
            'value'  => 'Taylor',
        ],
    ], Filter::only(
        Filter::field('name', ['$eq'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "name" = 'Taylor'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotEqualFilter | $notEq', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notEq',
            'target' => 'name',
            'value'  => 'Taylor',
        ],
    ], Filter::only(
        Filter::field('name', ['$notEq'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "name" != 'Taylor'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('GreaterThanFilter | $gt', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$gt',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', ['$gt'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" > 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('GreaterThanEqualToFilter | $gte', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$gte',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', ['$gte'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" >= 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LessThanFilter | $lt', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$lt',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', ['$lt'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" < 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LessThanEqualToFilter | $lte', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$lte',
            'target' => 'age',
            'value'  => 18,
        ],
    ], Filter::only(
        Filter::field('age', ['$lte'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" <= 18
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeFilter | $like', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', ['$like'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" LIKE '%Laravel%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeStartFilter | $like:start', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like:start',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', ['$like:start'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" LIKE 'Laravel%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('LikeEndFilter | $like:end', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$like:end',
            'target' => 'description',
            'value'  => 'Laravel',
        ],
    ], Filter::only(
        Filter::field('description', ['$like:end'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" LIKE '%Laravel'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeFilter | $notLike', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', ['$notLike'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" NOT LIKE '%Symfony%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeStartFilter | $notLike:start', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike:start',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', ['$notLike:start'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" NOT LIKE 'Symfony%'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotLikeEndFilter | $notLike:end', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$notLike:end',
            'target' => 'description',
            'value'  => 'Symfony',
        ],
    ], Filter::only(
        Filter::field('description', ['$notLike:end'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where "description" NOT LIKE '%Symfony'
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('OrFilter | $or', function (): void {
    $sql = Comment::filter([
        [
            'type'  => '$or',
            'value' => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'awesome',
                ],
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'boring',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field('content', ['$like'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "comments" where (("content" LIKE '%awesome%') or ("content" LIKE '%boring%'))
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NullFilter | $null', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$null',
            'target' => 'age',
            'value'  => true,
        ],
        [
            'type'   => '$null',
            'target' => 'weight',
            'value'  => false,
        ],
    ], Filter::only(
        Filter::field('age', ['$null']),
        Filter::field('weight', ['$null'])
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" is null and "weight" is not null
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('InFilter | $in', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$in',
            'target' => 'name',
            'value'  => ['Taylor', 'Otwell', ],
        ],
    ], Filter::only(
        Filter::field('name', ['$in']),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "name" in ('Taylor', 'Otwell')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotInFilter | $notIn', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notIn',
            'target' => 'name',
            'value'  => ['Nuno', 'Maduro', ],
        ],
    ], Filter::only(
        Filter::field('name', ['$notIn']),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "name" not in ('Nuno', 'Maduro')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('BetweenFilter | $between', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$between',
            'target' => 'age',
            'value'  => [18, 65, ],
        ],
    ], Filter::only(
        Filter::field('age', ['$between']),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" between 18 and 65
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('NotBetweenFilter | $notBetween', function (): void {
    $sql = Person::filter([
        [
            'type'   => '$notBetween',
            'target' => 'age',
            'value'  => [18, 65, ],
        ],
    ], Filter::only(
        Filter::field('age', ['$notBetween']),
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "people" where "age" not between 18 and 65
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('HasFilter | $has', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$has',
            'target' => 'comments',
            'value'  => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'awesome',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'comments',
            ['$has', ],
            Filter::only(
                Filter::field('content', ['$like'])
            )
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%awesome%')
        SQL;

    expect($sql)->toBe($expectedSql);

});

it('DoesntHasFilter | $doesntHas', function (): void {
    $sql = Project::filter([
        [
            'type'   => '$doesntHas',
            'target' => 'comments',
            'value'  => [
                [
                    'type'   => '$like',
                    'target' => 'content',
                    'value'  => 'boring',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'comments',
            ['$doesntHas', ],
            Filter::only(
                Filter::field('content', ['$like'])
            )
        )
    ))->toRawSql();

    $expectedSql = <<< SQL
        select * from "projects" where not exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%boring%')
        SQL;

    expect($sql)->toBe($expectedSql);

});
