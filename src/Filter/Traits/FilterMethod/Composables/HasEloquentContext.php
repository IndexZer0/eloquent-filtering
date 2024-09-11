<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;

trait HasEloquentContext
{
    protected EloquentContext $eloquentContext;

    public function setEloquentContext(EloquentContext $eloquentContext): void
    {
        $this->eloquentContext = $eloquentContext;
    }

    public function eloquentContext(): EloquentContext
    {
        return $this->eloquentContext;
    }
}
