<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

use IndexZer0\EloquentFiltering\Filter\Morph\MorphTypes;

trait HasMorphTypes
{
    protected MorphTypes $types;

    public function setMorphTypes(MorphTypes $types): void
    {
        $this->types = $types->keyBy('type');
    }

    public static function hasMorphTypesRules(): array
    {
        return [
            'types'           => ['required', 'array', 'min:1'],
            'types.*.type'    => ['required', 'string'],
            'types.*.value'   => ['array'],
            'types.*.value.*' => ['array'],
        ];
    }
}
