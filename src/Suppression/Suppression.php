<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Suppression;

use Closure;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;

class Suppression
{
    public static ?Closure $suppressionHandler = null;

    public static function honour(Closure $callback): void
    {
        try {
            $callback();
        } catch (SuppressibleException $se) {
            if ($se->shouldSuppress()) {
                return;
            }

            if (self::$suppressionHandler === null) {
                throw $se;
            }

            call_user_func(self::$suppressionHandler, $se);
        }
    }

    public static function handleUsing(?Closure $callback = null): void
    {
        self::$suppressionHandler = $callback;
    }

    public static function clearSuppressionHandler(): void
    {
        self::handleUsing();
    }
}
