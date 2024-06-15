# eloquent-filtering

![Filter example](/img/header.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/indexzer0/eloquent-filtering/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/indexzer0/eloquent-filtering/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/IndexZer0/eloquent-filtering/graph/badge.svg?token=34B3NIPBRM)](https://codecov.io/gh/IndexZer0/eloquent-filtering)
[![Total Downloads](https://img.shields.io/packagist/dt/indexzer0/eloquent-filtering.svg?style=flat-square)](https://packagist.org/packages/indexzer0/eloquent-filtering)

---

- **Avoid** writing custom query logic for filtering your models.

Features:

- Filter 
  - Many filter methods.
  - Filter by fields.
  - Filter by relationship existence.
    - Filter by fields on relationships.
      - No duplicated unnecessary exist clauses in queries.
  - Alias fields and relationships.
  - Specify filter types per field/relationship.
  - Filter json columns.
    - Json path wildcard support.
  - Custom filters.
- Sort
  - Sort by fields.
  - Alias fields.
- Terminology based on laravel eloquent query builder.

---

## Simple example with relationship filter.

```php
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;

class Product extends Model implements IsFilterable
{
    use Filterable;
    
    public function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('name', ['$eq']),
            Filter::relation('manufacturer', ['$has'])->includeRelationFields()
        );
    }
    
    public function manufacturer(): HasOne
    {
        return $this->hasOne(Manufacturer::class);
    }
}

class Manufacturer extends Model implements IsFilterable
{
    use Filterable;

    public function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('name', ['$eq'])
        );
    }
}

$filters = [
    [
        'target' => 'name',
        'type'   => '$eq',
        'value'  => 'TV',
    ],
    [
        'type'   => '$has',
        'target' => 'manufacturer',
        'value'  => [
            [
                'type'   => '$eq',
                'target' => 'name',
                'value'  => 'Sony',
            ]                
        ]        
    ]
];

$sql = Product::filter($filters)->toRawSql();
```

```sql
SELECT *
FROM "products"
WHERE "name" = 'TV'
  AND EXISTS (SELECT *
              FROM "manufacturers"
              WHERE "products"."manufacturer_id" = "manufacturers"."id"
                AND "name" = 'Sony')
```

---

- [Simple Example](#simple-example-with-relationship-filter)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Making Model Filterable](#making-model-filterable)
    - [Allowing Filters](#allowing-filters)
        - [Define On Model](#define-on-model)
        - [Define In Filter](#define-in-filter)
        - [Allowing All Filters](#allowing-all-filters)
        - [Including Relationship Model Filters](#including-relationship-model-filters)
    - [Filter Structure](#filter-structure)
    - [Available Filters](#available-filters)
        - [Field Filters](#field-filters)
        - [Relationship Filters](#relationship-filters)
        - [Condition Filters](#condition-filters)
    - [Custom Filters](#custom-filters)
        - [Custom Field Filter](#custom-field-filter)
        - [Fully Custom Filter](#fully-custom-filter)
    - [Digging Deeper](#digging-deeper)
        - [Config](#config)
        - [Default Allowed Filters](#default-allowed-filters)
        - [Aliasing Targets](#aliasing-targets)
        - [Json Path Wildcards](#json-path-wildcards)
        - [Specifying Allowed Types](#specifying-allowed-types)
        - [Suppressing Exceptions](#suppressing-exceptions)
        - [Suppression Hooks](#suppression-hooks)
        - [Condition Filters Note](#condition-filters-note)
    - [Error Handling](#error-handling)
- [Changelog](#changelog)

---

## Requirements

- PHP Version >= 8.2
- Laravel Version >= 10

---

## Installation

You can install the package via composer:

```bash
composer require indexzer0/eloquent-filtering
```

Run the `install` artisan command to publish the config and service provider:

```bash
php artisan eloquent-filtering:install
```

---

## Usage

### Making Model Filterable

- Implement `IsFilterable` interface.
- Use `Filterable` trait.
- Define `allowedFilters()` method.

```php
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;

class Product extends Model implements IsFilterable
{
    use Filterable;
    
    public function allowedFilters(): AllowedFilterList 
    {
        return Filter::only(
            Filter::field('name', ['$eq']),
        );
    }
}
```

---

### Allowing Filters

By default, all filters are disallowed.

You can specify allowed filters in two ways:

#### Define on model.

```php
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\SomeFiltersAllowed;

class Product extends Model implements IsFilterable
{
    use Filterable;
    
    public function allowedFilters(): SomeFiltersAllowed
    {
        return Filter::only(
            Filter::field('name', ['$eq', '$like']),
            Filter::relation(
                'manufacturer', 
                ['$has', '$doesntHas'],
                Filter::only(
                    Filter::field('name', ['$like'])
                )
            )
        );
    }
    
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
```

#### Define in `::filter()`

- Defining in `::filter()` method takes priority over `allowedFilters()` on the model.

```php
Product::filter(
    $filters,
    Filter::only(
        Filter::field('name', ['$eq']),
        Filter::relation(
            'manufacturer', 
            ['$has', '$doesntHas'],
            Filter::only(
                Filter::field('name', ['$like'])
            )
        )
    )
)->get();
```

#### Allowing All Filters

You can allow all filters using `Filter::all()`.

> [!NOTE]
> This feature is intended for use when you're not using any user supplied column names and have complete developer control over the filters being applied.

> [!CAUTION]
> Allowing all filters and using filters from a HTTP request can put you at risk of sql injection due to PHP PDO can only bind values, not column names.

```php
public function allowedFilters(): AllFiltersAllowed
{
    return Filter::all();
}
```

#### Including Relationship Model Filters

By default, when specifying an allowed relation filter, fields within that relationship are not included in the allowed filter list.

You can specify allowed filters inside a relation in two ways.

1. Define them within `Filter::relation()` as 3rd parameter.

```php
public function allowedFilters(): SomeFiltersAllowed
{
    return Filter::only(
        Filter::relation(
            'manufacturer', ['$has', '$doesntHas'],
            Filter::only(
                Filter::field('name', ['$like'])
            )
        )
    );
}
```

2. Use `->includeRelationFields()` on `Filter::relation()`.

This method instructs the package to look for `AllowedField` filters within the `allowedFilters()` method of the relation model.

```php
public function allowedFilters(): SomeFiltersAllowed
{
    return Filter::only(
        Filter::relation('manufacturer', ['$has', '$doesntHas'])->includeRelationFields()
    );
}
```

> [!IMPORTANT]
> The relationship method **MUST** have return type specified, and the related model **MUST** also implement `IsFilterable`. 

---

### Filter Structure

- Filters **ALWAYS** have a `type`.
- All filters apart from `$or` and `$and` have a `target`.
- Filter `value` is different depending on the filter.

---

### Available Filters

This package provides core filters that give you the ability to perform the vast majority of the filtering you'd need.

#### Field Filters

| Filter                                                                  | Code                 | Query                                                                 |
|-------------------------------------------------------------------------|----------------------|-----------------------------------------------------------------------|
| [EqualFilter](#EqualFilter---eq)                                        | `$eq`                | `{$target} = {$value}`                                                |
| [NotEqualFilter](#NotEqualFilter---noteq)                               | `$notEq`             | `{$target} != {$value}`                                               |
| [GreaterThanFilter](#GreaterThanFilter---gt)                            | `$gt`                | `{$target} > {$value}`                                                |
| [GreaterThanEqualToFilter](#GreaterThanEqualToFilter---gte)             | `$gte`               | `{$target} >= {$value}`                                               |
| [LessThanFilter](#LessThanFilter---lt)                                  | `$lt`                | `{$target} < {$value}`                                                |
| [LessThanEqualToFilter](#LessThanEqualToFilter---lte)                   | `$lte`               | `{$target} <= {$value}`                                               |
| [LikeFilter](#LikeFilter---like)                                        | `$like`              | `{$target} LIKE '%{$value}%'`                                         |
| [LikeStartFilter](#LikeStartFilter---likestart)                         | `$like:start`        | `{$target} LIKE '{$value}%'`                                          |
| [LikeEndFilter](#LikeEndFilter---likeend)                               | `$like:end`          | `{$target} LIKE '%{$value}'`                                          |
| [NotLikeFilter](#NotLikeFilter---notlike)                               | `$notLike`           | `{$target} NOT LIKE '%{$value}%'`                                     |
| [NotLikeStartFilter](#NotLikeStartFilter---notlikestart)                | `$notLike:start`     | `{$target} NOT LIKE '{$value}%'`                                      |
| [NotLikeEndFilter](#NotLikeEndFilter---notlikeend)                      | `$notLike:end`       | `{$target} NOT LIKE '%{$value}'`                                      |
| [NullFilter](#NullFilter---null)                                        | `$null`              | `{$target} is null` <code>&#124;&#124;</code> `{$target} is not null` |
| [InFilter](#InFilter---in)                                              | `$in`                | `{$target} in ($value)`                                               |
| [NotInFilter](#NotInFilter---notin)                                     | `$notIn`             | `{$target} not in ($value)`                                           |
| [BetweenFilter](#BetweenFilter---between)                               | `$between`           | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenFilter](#NotBetweenFilter---notbetween)                      | `$notBetween`        | `{$target} not between $value[0] and $value[1]`                       |
| [BetweenColumnsFilter](#BetweenColumnsFilter---betweencolumns)          | `$betweenColumns`    | `{$target} between $value[0] and $value[1]`                           |
| [NotBetweenColumnsFilter](#NotBetweenColumnsFilter---notbetweencolumns) | `$notBetweenColumns` | `{$target} not between $value[0] and $value[1]`                       |
| [JsonContainsFilter](#JsonContainsFilter---jsoncontains)                | `$jsonContains`      | `json_contains({$target}, {$value})`                                  |
| [JsonNotContainsFilter](#JsonNotContainsFilter---jsonnotcontains)       | `$jsonNotContains`   | `not json_contains({$target}, {$value})`                              |
| [JsonLengthFilter](#JsonLengthFilter---jsonlength)                      | `$jsonLength`        | `json_length({$target}}) $operator $value`                            |

#### Relationship Filters

| Filter                                          | Code             | Query                                                                 |
|-------------------------------------------------|------------------|-----------------------------------------------------------------------|
| [HasFilter](#HasFilter---has)                   | `$has`           | `where exists (select * from {$target})`                              |
| [DoesntHasFilter](#DoesntHasFilter---doesnthas) | `$doesntHas`     | `where not exists (select * from {$target})`                          |

#### Condition Filters

| Filter                        | Code   | Query |
|-------------------------------|--------|-------|
| [OrFilter](#OrFilter---or)    | `$or`  | `or`  |
| [AndFilter](#AndFilter---and) | `$and` | `and` |

See [Conditional Filters Note](#condition-filters-note)

- Accepting pull requests for more common filters.

---

#### Filter Examples

#### EqualFilter - `$eq`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$eq',
        'target' => 'name',
        'value'  => 'Taylor',
    ]
])->toRawSql();
```

```sql
select * from "people" where "name" = 'Taylor'
```

#### NotEqualFilter - `$notEq`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$notEq',
        'target' => 'name',
        'value'  => 'Taylor',
    ]
])->toRawSql();
```

```sql
select * from "people" where "name" != 'Taylor'
```

#### GreaterThanFilter - `$gt`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$gt',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" > 18
```

#### GreaterThanEqualToFilter - `$gte`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$gte',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" >= 18
```

#### LessThanFilter - `$lt`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$lt',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" < 18
```

#### LessThanEqualToFilter - `$lte`

- `value` = `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$lte',
        'target' => 'age',
        'value'  => 18,
    ]
])->toRawSql();
```

```sql
select * from "people" where "age" <= 18
```

#### LikeFilter - `$like`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE '%Laravel%'
```

#### LikeStartFilter - `$like:start`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like:start',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE 'Laravel%'
```

#### LikeEndFilter - `$like:end`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$like:end',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" LIKE '%Laravel'
```

#### NotLikeFilter - `$notLike`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE '%Laravel%'
```

#### NotLikeStartFilter - `$notLike:start`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike:start',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE 'Laravel%'
```

#### NotLikeEndFilter - `$notLike:end`

- `value` = `string` | `int` | `float`.

```php
$sql = Project::filter([
    [
        'type'   => '$notLike:end',
        'target' => 'description',
        'value'  => 'Laravel',
    ]
])->toRawSql();
```

```sql
select * from "projects" where "description" NOT LIKE '%Laravel'
```

#### NullFilter - `$null`

- `value` = `boolean` .
- `true` for `is null`.
- `false` for `is not null`.

```php
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
])->toRawSql();
```

```sql
select * from "people" where "age" is null and "weight" is not null
```

#### InFilter - `$in`

- `value` = array of `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$in',
        'target' => 'name',
        'value'  => ['Taylor', 'Otwell',]
    ],
])->toRawSql();
```

```sql
select * from "people" where "name" in ('Taylor', 'Otwell')
```

#### NotInFilter - `$notIn`

- `value` = array of `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$notIn',
        'target' => 'name',
        'value'  => ['Nuno', 'Maduro',]
    ],
])->toRawSql();
```

```sql
select * from "people" where "name" not in ('Nuno', 'Maduro')
```

#### BetweenFilter - `$between`

- `value` = array of `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$between',
        'target' => 'age',
        'value'  => [18, 65,],
    ],
])->toRawSql();
```

```sql
select * from "people" where "age" between 18 and 65
```

#### NotBetweenFilter - `$notBetween`

- `value` = array of `string` | `int` | `float`.

```php
$sql = Person::filter([
    [
        'type'   => '$notBetween',
        'target' => 'age',
        'value'  => [18, 65,],
    ],
])->toRawSql();
```

```sql
select * from "people" where "age" not between 18 and 65
```

#### BetweenColumnsFilter - `$betweenColumns`

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'type'   => '$betweenColumns',
        'target' => 'price',
        'value'  => [
            'min_allowed_price',
            'max_allowed_price',
        ],
    ],
])->toRawSql();
```

```sql
select * from "products" where "price" between "min_allowed_price" and "max_allowed_price"
```

#### NotBetweenColumnsFilter - `$notBetweenColumns`

- `value` = `array` of strings.

```php
$sql = Product::filter([
    [
        'type'   => '$notBetweenColumns',
        'target' => 'price',
        'value'  => [
            'min_allowed_price',
            'max_allowed_price',
        ],
    ],
])->toRawSql();
```

```sql
select * from "products" where "price" between "min_allowed_price" and "max_allowed_price"
```

#### JsonContainsFilter - `$jsonContains`

- `value` = `string` | `int` | `float`.

```php
$sql = User::filter([
    [
        'type'   => '$jsonContains',
        'target' => 'options->languages',
        'value'  => 'en',
    ],
])->toRawSql();
```

```sql
select * from "users" where json_contains(`options`, '\"en\"', '$."languages"')
```

#### JsonNotContainsFilter - `$jsonNotContains`

- `value` = `string` | `int` | `float`.

```php
$sql = User::filter([
    [
        'type'   => '$jsonNotContains',
        'target' => 'options->languages',
        'value'  => 'en',
    ],
])->toRawSql();
```

```sql
select * from "users" where not json_contains(`options`, '\"en\"', '$."languages"')
```

#### JsonLengthFilter - `$jsonLength`

- `operator` = `=` |  `<` | `<=` | `>` | `>=`.
- `value` = `int`.

```php
$sql = User::filter([
    [
        'type'     => '$jsonLength',
        'target'   => 'options->languages',
        'operator' => '>=',
        'value'    => 2,
    ],
])->toRawSql();
```

```sql
select * from "users" where json_length(`options`, '$."languages"') >= 2
```

---

#### HasFilter - `$has`

- `value` = `array` of filters.

```php
$sql = Project::filter([
    [
        'type'   => '$has',
        'target' => 'comments',
        'value'  => [
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'awesome',
            ]
        ]
    ],
])->toRawSql();
```

```sql
select * from "projects" where exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%awesome%')
```

#### DoesntHasFilter - `$doesntHas`

- `value` = `array` of filters.

```php
$sql = Project::filter([
    [
        'type'   => '$doesntHas',
        'target' => 'comments',
        'value'  => [
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'boring',
            ]
        ]
    ],
])->toRawSql();
```

```sql
select * from "projects" where not exists (select * from "comments" where "projects"."id" = "comments"."project_id" and "content" LIKE '%boring%')
```

---

#### OrFilter - `$or`

- `value` = `array` of filters.

```php
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
            ]
        ]
    ]
])->toRawSql();
```

```sql
select * from "comments" where (("content" LIKE '%awesome%') or ("content" LIKE '%boring%'))
```

#### AndFilter - `$and`

- `value` = `array` of filters.

```php
$sql = Comment::filter([
    [
        'type'  => '$and',
        'value' => [
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'is awesome',
            ],
            [
                'type'   => '$like',
                'target' => 'content',
                'value'  => 'is not boring',
            ]
        ]
    ]
])->toRawSql();
```

```sql
select * from "comments" where (("content" LIKE '%is awesome%') and ("content" LIKE '%is not boring%'))
```

---

### Custom Filters

- You can create two different types of custom filters.
    - Custom Field Filter
    - Fully Custom Filter

> [!IMPORTANT]
> You must register your custom filter classes in the config file `eloquent-filtering.php`

```php
'custom_filters' => [
    YourCustomFilter::class,
],
```

#### Custom Field Filter

- Usage: `Filter::field('name', ['$lowercase'])`.

```bash
php artisan make:eloquent-filter LowerCaseFilter --type=field
```

```php
class LowerCaseFilter extends AbstractFieldFilter
{
    final public function __construct(
        protected string $target,
        protected string $value,
    ) {

    }
    
    /*
     * The unique identifier of the filter.
     */
    public static function type(): string
    {
        return '$lowercase';
    }

    /*
     * The format that the filter data must adhere to.
     * Defined as laravel validator rules.
     * On fail: throws MalformedFilterFormatException.
     */
    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'string'],
        ];
    }

    /*
     * Instantiate filter class from ApprovedFilter.
     */
    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->data_get('value'),
        );
    }

    /*
     * Apply the filter logic.
     */
    public function apply(Builder $query): Builder
    {
        return $query->where(
            DB::raw("LOWER({$this->target})"),
            strtolower($this->value)
        );
    }
}

/*
 * Usage:
 */

public function allowedFilters(): SomeFiltersAllowed
{
    return Filter::only(
        Filter::field('name', ['$lowercase']),
    );
}
```

#### Fully Custom Filter

- Generally for use when there is no user specified target field.

```bash
php artisan make:eloquent-filter AdminFilter --type=custom
```

```php
class AdminFilter extends AbstractCustomFilter
{
    /*
     * The unique identifier of the filter.
     */
    public static function type(): string
    {
        return '$admin';
    }

    /*
     * Apply the filter logic.
     */
    public function apply(Builder $query): Builder
    {
        return $query->where('admin', true);
    }
}

/*
 * Usage:
 */

public function allowedFilters(): SomeFiltersAllowed
{
    return Filter::only(
        Filter::custom(['$admin']),
    );
}
```

### Digging Deeper

#### Config

- Default configuration file

```php
return [
    'default_allowed_filter_list' => 'none',
    'default_allowed_sort_list'   => 'none',

    'suppress' => [
        'filter' => [
            'invalid'          => false,
            'missing'          => false,
            'malformed_format' => false,
            'denied'           => false,
        ],
        'sort' => [
            'malformed_format' => false,
            'denied'           => false,
        ],
    ],

    'custom_filters' => [

    ],
];
```

- The package throws various exception which can be suppressed.
- Custom filters should be registered in the config.

#### Default Allowed Filters

> [!NOTE]
> This feature is intended for use when you're not using any user supplied column names and have complete developer control over the filters being applied.

You can change the default allowed filters within the config file `eloquent-filtering.php`.

```php
'default_allowed_filter_list' => 'all',
```

> [!CAUTION]
> Allowing all filters by default and using filters from a HTTP request can put you at risk of sql injection due to PHP PDO can only bind values, not column names.

It is strongly suggested that you keep `default_allowed_filter_list` as `none` in your config and explicitly allow only specific filters with `Filter::only()`.

#### Aliasing Targets

You can alias your target fields and relations if you don't wish to expose database field names and relationship method names to your frontend.

The below example:
 - Allows `name` and uses `first_name` in the database query.
 - Allows `documents` and uses `files` as the relationship name.

```php
$sql = Person::filter([
    [
        'type'   => '$eq',
        'target' => 'name',
        'value'  => 'Taylor',
    ],
    [
        'type'   => '$has',
        'target' => 'documents',
        'value'  => [],
    ],
], Filter::only(
    Filter::field(Target::alias('name', 'first_name'), ['$eq']),
    Filter::relation(Target::alias('documents', 'files'), ['$has'])
))->toRawSql();
```

You can also alias targets when allowing all filters.

```php
Filter::all(
    Target::alias('name', 'first_name'),
    Target::relationAlias(
        'documents',
        'files',
        Target::alias('file_extension', 'mime_type')
    ),
)
```

#### Json Path Wildcards

- When specifying the target of a json database field you can specify wildcards in the json path.

```php
Filter::only(
    Filter::field('data->*->array', ['$jsonContains']),
)

/*
 * Allows:
 */
$filters = [
    [
        'type'   => '$jsonContains',
        'target' => 'data->languages->array',
        'value'  => [
            'en',
            'de',
        ]
    ]
];
```

#### Specifying Allowed Types

```php
use IndexZer0\EloquentFiltering\Filter\Types\Types;

// Only `$eq` allowed
Filter::field('name', ['$eq'])
Filter::field('name', Types::only(['$eq']))

// All types allowed
Filter::field('name', Types::all()),

// All except `$eq` allowed
Filter::field('name', Types::except(['$eq'])),
```

#### Suppressing Exceptions

Various exceptions are thrown by this package. Most can be suppressed globally in the config file.

When suppressing an exception, filters that caused the exception will be ignored.

- Suppressible

```php
class InvalidFilterException
config("eloquent-filtering.suppress.filter.invalid");
// Filter does not have `type` key.

class MissingFilterException
config("eloquent-filtering.suppress.filter.missing");
// Can't find filter of `type` specified.

class MalformedFilterFormatException
config("eloquent-filtering.suppress.filter.malformed_format");
// The filter was found, but the rest of the data does not match required format of the filter.

class DeniedFilterException
config("eloquent-filtering.suppress.filter.denied");
// Filter is not allowed.
``` 

- Not Suppressible

```php
class DuplicateFiltersException
// When you have registered a custom filter that has the same type as another filter.
``` 

#### Suppression Hooks

You can hook into the suppression system if you want to perform some custom actions.

```php
use IndexZer0\EloquentFiltering\Suppression\Suppression;

Suppression::handleDeniedFilterUsing(function (SuppressibleException $se): void {
    Log::channel('slack')->info('Bug in frontend client, trying to use filter type that is not allowed: ' . $se->getMessage());
    throw new FrontendBugException($se->getMessage());
});
```

Available suppression hooks.

```php
// All
Suppression::handleAllUsing();
// Filter
Suppression::handleFilterUsing();
Suppression::handleInvalidFilterUsing();
Suppression::handleMissingFilterUsing();
Suppression::handleMalformedFilterUsing();
Suppression::handleDeniedFilterUsing();
// Sort
Suppression::handleSortUsing();
Suppression::handleMalformedSortUsing();
Suppression::handleDeniedSortUsing();
```

#### Condition Filters Note

The condition filters `$or`, and `$and` are not required to be specified when allowing filters.

These filters are always allowed, due to these filters essentially being wrappers around other filters.

---

### Error Handling

All exceptions thrown by the package implement `\IndexZer0\EloquentFiltering\Contracts\EloquentFilteringException`.

How-ever it doesn't harm to also catch `\Throwable`.

```php
try {
    Person::filter([])->get();
} catch (\IndexZer0\EloquentFiltering\Contracts\EloquentFilteringException $exception) {
    $exception->getMessage(); 
} catch (\Throwable $t) {
    // Shouldn't happen - but failsafe.
}
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Credits

- [IndexZer0](https://github.com/IndexZer0)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
