<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Context;

enum FilterContext: string
{
    case FIELD = 'field';
    case RELATION = 'relation';
    case CUSTOM = 'custom';
    case CONDITION = 'condition';
}
