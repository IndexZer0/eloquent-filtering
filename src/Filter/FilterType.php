<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;

enum FilterType: string
{
    /*
    |--------------------------------------------------------------------------
    | Field Filters
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Conditional Filters
    |--------------------------------------------------------------------------
    */

    case OR = '$or';
    case AND = '$and';

    /*
    |--------------------------------------------------------------------------
    | Relationship Filters
    |--------------------------------------------------------------------------
    */

    case HAS = '$has';
    case DOESNT_HAS = '$doesntHas';

    /*
    |--------------------------------------------------------------------------
    | Morph Relationship Filters
    |--------------------------------------------------------------------------
    */

    case HAS_MORPH = '$hasMorph';
    case DOESNT_HAS_MORPH = '$doesntHasMorph';

    public function withModifiers(string ...$modifiers): AllowedType
    {
        return $this->toAllowedType()->withModifiers(...$modifiers);
    }

    public function withoutModifiers(): AllowedType
    {
        return $this->toAllowedType()->withModifiers();
    }

    public function withValidation(
        array $rules,
        array $messages = [],
        array $attributes = []
    ): AllowedType {
        return $this->toAllowedType()->withValidation($rules, $messages, $attributes);
    }

    public function toAllowedType(): AllowedType
    {
        return new AllowedType($this->value);
    }
}
