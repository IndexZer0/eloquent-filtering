<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Suppression;

use Closure;
use Illuminate\Support\Str;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;

class Suppression
{
    protected static array $suppressionHandlers = [];

    public static function honour(Closure $callback): void
    {
        try {
            $callback();
        } catch (SuppressibleException $se) {
            self::executeCustomHandler($se);

            if ($se->isSuppressed()) {
                return;
            }

            throw $se;
        }
    }

    private static function executeCustomHandler(SuppressibleException $se): void
    {
        $keepChecking = true;
        $suppressionKey = $se->suppressionKey();

        while ($keepChecking) {
            $keepChecking = self::shouldKeepChecking($suppressionKey);

            $handler = self::getHandler($suppressionKey);

            $suppressionKey = self::removeLastPart($suppressionKey);

            if (is_callable($handler)) {
                $handler($se);
                return;
            }
        };
    }

    private static function shouldKeepChecking(string $suppressionKey): bool
    {
        return str_contains($suppressionKey, '.');
    }

    private static function getHandler(string $suppressionKey): ?callable
    {
        if (array_key_exists($suppressionKey, self::$suppressionHandlers)) {
            return self::$suppressionHandlers[$suppressionKey];
        }
        return null;
    }

    private static function removeLastPart(string $suppressionKey): string
    {
        return Str::of($suppressionKey)->beforeLast('.')->toString();
    }

    /*
     * All
     */

    public static function handleAllUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress'] = $callback;
    }

    /*
     * Filter
     */

    public static function handleFilterUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.filter'] = $callback;
    }

    public static function handleInvalidFilterUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.filter.invalid'] = $callback;
    }

    public static function handleMissingFilterUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.filter.missing'] = $callback;
    }

    public static function handleMalformedFilterUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.filter.malformed_format'] = $callback;
    }

    public static function handleDeniedFilterUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.filter.denied'] = $callback;
    }

    /*
     * Sort
     */

    public static function handleSortUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.sort'] = $callback;
    }

    public static function handleMalformedSortUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.sort.malformed_format'] = $callback;
    }

    public static function handleDeniedSortUsing(?callable $callback = null): void
    {
        self::$suppressionHandlers['suppress.sort.denied'] = $callback;
    }

    /*
     * Clear
     */

    public static function clearSuppressionHandlers(): void
    {
        self::$suppressionHandlers = [];
    }
}
