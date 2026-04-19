<?php

return [

    'default' => 'main',

    'connections' => [

        'main' => [
            'salt' => env('HASHIDS_SALT', 'gg-case-store-secret'),
            'length' => 10,
        ],

    ],

];
