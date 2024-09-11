# Feature Ideas

---

## Filter

### Config
- Max filter amount
- Max nested depth. (for protection against `$or` and `$and` nesting).
- Disable/enable `$or` and `$and` globally.

### Add a general $where filter that takes operator.
```php
$filter = [
    'target'   => 'column',
    'type'     => '$where',
    'operator' => '=', // Rule::in(['=', '<', '<=', '>', '>='])
    'value'    => 'value',
],
```

### Provide more core filters.

- `$trashed` - for soft deletes.
```php
// e.g.
$filter = [
    'type' => '$trashed',
    'value' => 'all' | 'true' | 'false'
]

$filter = [
    'type' => '$onlyTrashed'
]

$filter = [
    'type' => '$withTrashed'
]
```
- `$latest` - for order by.
```php
// e.g.
$filter = [
    'type' => '$latest',
    'value' => 'created_at',
]

$filter = [
    'type' => '$latest'
]
```

- `$empty` / `$blank`.
```php
$filter = [
    'type' => '$empty'
]
// where column = '' or column is null
```

- `$filled`.
```php
$filter = [
    'type' => '$filled'
]
// where column != '' and column is not null
```

### Scope filters?

```php
Filter::scope('active')
// Would call $model->active() - ( scopeActive() )
```

### Default filter types.

A way to use a filter without specifying the type?

- Something like:
```php
Filter::field('name', [FilterType::EQUALS, FilterType::LIKE])
    ->default(FilterType::EQUALS)
```

### Filter Sets

- Ability to define multiple sets of allowed filters that can be used in different parts of an application.
  - Define filters in dedicated classes and register them on model.

### Join

- Ability to apply the relationship filters to a join.

---

## Sort

- Sort by relationship column.
