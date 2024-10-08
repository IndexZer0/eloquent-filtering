<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use InvalidArgumentException;

class InvalidFiltersPayloadException extends InvalidArgumentException implements FilterException
{
}
