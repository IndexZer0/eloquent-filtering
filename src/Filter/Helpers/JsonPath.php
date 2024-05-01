<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Helpers;

class JsonPath
{
    public function __construct(private string $path)
    {

    }

    public static function of(string $path): self
    {
        return new self($path);
    }

    public function allows(string $target)
    {
        if (!str_contains($this->path, '*')) {
            // Check if the requested path exactly matches the allowed path
            return $this->path === $target;
        }

        if (str_ends_with($target, '->')) {
            return false;
        }

        // Convert both paths into arrays of segments
        $allowedSegments = explode('->', $this->path);
        $requestedSegments = explode('->', $target);

        if ($this->amountOfSegmentsDiffer($allowedSegments, $requestedSegments)) {
            return false;
        }

        // Check each segment in the requested path
        foreach ($allowedSegments as $key => $segment) {
            // If the segment in the requested path is not a wildcard
            if ($segment !== '*' && $segment !== $requestedSegments[$key]) {
                return false; // Fail the logic check
            }
        }

        // Convert allowed path to regex pattern
        $pattern = preg_quote($this->path, '/');
        $pattern = str_replace('\*', '.*', $pattern); // Replace * with .*

        // Add regex anchors
        $pattern = '/^' . $pattern . '$/';

        // Check if the requested path matches the regex pattern
        return (bool) preg_match($pattern, $target);
    }

    private function amountOfSegmentsDiffer(array $one, array $two): bool
    {
        return count($one) != count($two);
    }
}
