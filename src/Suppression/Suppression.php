<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Suppression;

use Closure;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;

class Suppression
{
    public static function honour(Closure $callback): void
    {
        try {
            $callback();
        } catch (SuppressibleException $fe) {
            if (!$fe->shouldSuppress()) {
                throw $fe;
            }
        }
    }
}
