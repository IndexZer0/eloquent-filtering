<?php

declare(strict_types=1);

return [
    'default_allowed_sort_list' => 'none',

    'suppress' => [
        'filter' => [
            'invalid'          => false,
            'missing'          => false,
            'malformed_format' => false,
            'denied'           => false,
        ],
        'sort' => [
            'malformed_format' => false,
            'denied'           => false,
        ],
    ],

    'custom_filters' => [

    ],
];
