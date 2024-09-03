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

### Provide core filters for common laravel queries.

- `$trashed`.
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
- `$latest`.
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
- etc

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
