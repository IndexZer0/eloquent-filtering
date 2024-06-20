# Feature Ideas

---

## Filter

### Config
- Max filter amount
- Max nested depth. (for protection against `$or` and `$and` nesting).
- Disable/enable `$or` and `$and` globally.

### Types
- Enum for types.

### Null

- Make some filters (such as `$in`) cleverly handle `null`.

### Filter Sets

- Ability to define multiple sets of allowed filters that can be used in different parts of an application.
  - Define filters in dedicated classes and register them on model.

### Validation

Something like:

```php
Filter::field('status', ['$in'])->withRules([Rule::enum(Status::class)]);
```

### Join

- Ability to apply the relationship filters to a join.

---

## Sort

- Sort by relationship column.
