<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;

enum FilterType: string
{
    // Equal
    case EQUAL = '$eq';
    case NOT_EQUAL = '$notEq';

    // Greater Than
    case GREATER_THAN = '$gt';
    case GREATER_THAN_EQUAL_TO = '$gte';

    // Less Than
    case LESS_THAN = '$lt';
    case LESS_THAN_EQUAL_TO = '$lte';

    // Like
    case LIKE = '$like';
    case NOT_LIKE = '$notLike';

    // Conditional
    case OR = '$or';
    case AND = '$and';

    // Null
    case NULL = '$null';

    // In
    case IN = '$in';
    case NOT_IN = '$notIn';

    // Between
    case BETWEEN = '$between';
    case NOT_BETWEEN = '$notBetween';

    // Between Columns
    case BETWEEN_COLUMNS = '$betweenColumns';
    case NOT_BETWEEN_COLUMNS = '$notBetweenColumns';

    // Json
    case JSON_CONTAINS = '$jsonContains';
    case JSON_NOT_CONTAINS = '$jsonNotContains';
    case JSON_LENGTH = '$jsonLength';

    // Relationship
    case HAS = '$has';
    case DOESNT_HAS = '$doesntHas';

    public function withModifiers(string ...$modifiers): AllowedType
    {
        return $this->toAllowedType()->withModifiers(...$modifiers);
    }

    public function withoutModifiers(): AllowedType
    {
        return $this->toAllowedType()->withModifiers();
    }

    public function withRules(array $rules): AllowedType
    {
        return $this->toAllowedType()->withRules($rules);
    }

    public function toAllowedType(): AllowedType
    {
        return new AllowedType($this->value);
    }
}
