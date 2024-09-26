<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\BlogPost;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany\Role;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany\User;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany\Individual;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany\Task;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Post;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Tag;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Video;

beforeEach(function (): void {
    $this->createPostTagAndPivotRecords();
    $this->createUserRolePivotRecords();
    $this->createManyToManyPivotRecords();
});

it('can filter by pivot field when allowed', function (): void {

    $postHasTagQuery = Post::filter([
        [
            'target' => 'tags',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tags',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        ),
    ));

    $postHasTagQueryExpectedSql = <<< SQL
        select * from "posts" where exists (select * from "tags" inner join "post_tag" on "tags"."id" = "post_tag"."tag_id" where "posts"."id" = "post_tag"."post_id" and "tags"."name" = 'tag-name-1' and "post_tag"."tagged_by" = 'tagged-by-user-1')
        SQL;

    expect($postHasTagQuery->toRawSql())->toBe($postHasTagQueryExpectedSql);

    $postHasTagModels = $postHasTagQuery->get();

    expect($postHasTagModels->count())->toBe(1)
        ->and($postHasTagModels->first()->title)->toBe('post-title-1');

    $tagHasPostQuery = Tag::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'tag-name-1',
        ],
        [
            'target' => 'posts',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::EQUAL]),
        Filter::relation(
            'posts',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Tag::class),
            ),
        ),
    ));

    $tagHasPostQueryExpectedSql = <<< SQL
        select * from "tags" where "tags"."name" = 'tag-name-1' and exists (select * from "posts" inner join "post_tag" on "posts"."id" = "post_tag"."post_id" where "tags"."id" = "post_tag"."tag_id" and "post_tag"."tagged_by" = 'tagged-by-user-1')
        SQL;

    expect($tagHasPostQuery->toRawSql())->toBe($tagHasPostQueryExpectedSql);

    $tagHasPostModels = $tagHasPostQuery->get();

    expect($tagHasPostModels->count())->toBe(1)
        ->and($tagHasPostModels->first()->name)->toBe('tag-name-1');

});

it('ors with pivot', function (): void {

    $query = Post::filter([
        [
            'target' => 'tags',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'type'  => '$or',
                    'value' => [
                        [
                            'target' => 'tagged_by',
                            'type'   => '$eq',
                            'value'  => 'tagged-by-user-1',
                        ],
                        [
                            'target' => 'tagged_by',
                            'type'   => '$eq',
                            'value'  => 'tagged-by-user-2',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tags',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "posts" where exists (select * from "tags" inner join "post_tag" on "tags"."id" = "post_tag"."tag_id" where "posts"."id" = "post_tag"."post_id" and "tags"."name" = 'tag-name-1' and (("post_tag"."tagged_by" = 'tagged-by-user-1') or ("post_tag"."tagged_by" = 'tagged-by-user-2')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->title)->toBe('post-title-1');

});

it('can not use pivot filter when not in context of a relationship', function (): void {

    Tag::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'tag-name-1',
        ],
        [
            'target' => 'tagged_by',
            'type'   => '$eq',
            'value'  => 'tagged-by-user-1',
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::EQUAL]),
        Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');

it('can not use pivot filter when in context of different relationship (BelongsTo)', function (): void {

    Video::filter([
        [
            'target' => 'tag',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tag',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        ),
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');

it('can not use pivot filter when in context of different relationship (BelongsToMany)', function (): void {

    BlogPost::filter([
        [
            'target' => 'tags',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tags',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        ),
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');

it('can include pivot filters from custom intermediate table models (BelongsToMany)', function (): void {

    $userHasRoleQuery = User::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'user-name-1',
        ],
        [
            'target' => 'roles',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'role-name-1',
                ],
                [
                    'target' => 'assigned_by',
                    'type'   => '$eq',
                    'value'  => 'user-name-2',
                ],
            ],
        ],
    ]);

    $userHasRoleQueryExpectedSql = <<< SQL
        select * from "users" where "users"."name" = 'user-name-1' and exists (select * from "roles" inner join "role_user" on "roles"."id" = "role_user"."role_id" where "users"."id" = "role_user"."user_id" and "roles"."name" = 'role-name-1' and "role_user"."assigned_by" = 'user-name-2')
        SQL;

    expect($userHasRoleQuery->toRawSql())->toBe($userHasRoleQueryExpectedSql);

    $userHasRoleModels = $userHasRoleQuery->get();

    expect($userHasRoleModels->count())->toBe(1)
        ->and($userHasRoleModels->first()->name)->toBe('user-name-1');

    $roleHasUserQuery = Role::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'role-name-2',
        ],
        [
            'target' => 'users',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'user-name-2',
                ],
                [
                    'target' => 'assigned_by',
                    'type'   => '$eq',
                    'value'  => 'user-name-1',
                ],
            ],
        ],
    ]);

    $roleHasUserQueryExpectedSql = <<< SQL
        select * from "roles" where "roles"."name" = 'role-name-2' and exists (select * from "users" inner join "role_user" on "users"."id" = "role_user"."user_id" where "roles"."id" = "role_user"."role_id" and "users"."name" = 'user-name-2' and "role_user"."assigned_by" = 'user-name-1')
        SQL;

    expect($roleHasUserQuery->toRawSql())->toBe($roleHasUserQueryExpectedSql);

    $roleHasUserModels = $roleHasUserQuery->get();

    expect($roleHasUserModels->count())->toBe(1)
        ->and($roleHasUserModels->first()->name)->toBe('role-name-2');

});

it('can include pivot filters from custom intermediate table models (MorphToMany)', function (): void {

    $taskHasQuery = Task::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'task-name-1',
        ],
        [
            'target' => 'individuals',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'individual-name-1',
                ],
                [
                    'target' => 'assigned_by',
                    'type'   => '$eq',
                    'value'  => 'individual-name-2',
                ],
            ],
        ],
        [
            'target' => 'groups',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'group-name-1',
                ],
                [
                    'target' => 'assigned_by',
                    'type'   => '$eq',
                    'value'  => 'group-name-2',
                ],
            ],
        ],
    ]);

    $taskHasQueryExpectedSql = <<< SQL
        select * from "tasks" where "tasks"."name" = 'task-name-1' and exists (select * from "individuals" inner join "taskables" on "individuals"."id" = "taskables"."taskable_id" where "tasks"."id" = "taskables"."task_id" and "taskables"."taskable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany\Individual' and "individuals"."name" = 'individual-name-1' and "taskables"."assigned_by" = 'individual-name-2') and exists (select * from "groups" inner join "taskables" on "groups"."id" = "taskables"."taskable_id" where "tasks"."id" = "taskables"."task_id" and "taskables"."taskable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany\Group' and "groups"."name" = 'group-name-1' and "taskables"."assigned_by" = 'group-name-2')
        SQL;

    expect($taskHasQuery->toRawSql())->toBe($taskHasQueryExpectedSql);

    $taskHasModels = $taskHasQuery->get();

    expect($taskHasModels->count())->toBe(1)
        ->and($taskHasModels->first()->name)->toBe('task-name-1');

    $individualHasQuery = Individual::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'individual-name-1',
        ],
        [
            'target' => 'tasks',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'task-name-1',
                ],
                [
                    'target' => 'assigned_by',
                    'type'   => '$eq',
                    'value'  => 'individual-name-2',
                ],
            ],
        ],
    ]);

    $taskHasQueryExpectedSql = <<< SQL
        select * from "individuals" where "individuals"."name" = 'individual-name-1' and exists (select * from "tasks" inner join "taskables" on "tasks"."id" = "taskables"."task_id" where "individuals"."id" = "taskables"."taskable_id" and "taskables"."taskable_type" = 'IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\MorphToMany\Individual' and "tasks"."name" = 'task-name-1' and "taskables"."assigned_by" = 'individual-name-2')
        SQL;

    expect($individualHasQuery->toRawSql())->toBe($taskHasQueryExpectedSql);

    $individualHasModels = $individualHasQuery->get();

    expect($individualHasModels->count())->toBe(1)
        ->and($individualHasModels->first()->name)->toBe('individual-name-1');

});
