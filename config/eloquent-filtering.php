<?php

declare(strict_types=1);

return [
    'default_allowed_filter_list' => 'none',
    'default_allowed_sort_list' => 'none',

    'suppress' => [
        'filter' => [
            'denied'           => false,
            'missing'          => false,
            'invalid'          => false,
            'malformed_format' => false,
        ],
        'sort' => [
            'denied' => false,
        ],
    ],

    'custom_filters' => [

    ],
];
