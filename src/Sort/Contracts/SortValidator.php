<?php

namespace IndexZer0\EloquentFiltering\Sort\Contracts;

use IndexZer0\EloquentFiltering\Sort\PendingSortCollection;

interface SortValidator
{
    public function validate(array $sorts): PendingSortCollection;
}
