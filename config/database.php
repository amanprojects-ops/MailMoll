<?php

return [
    'default' => env('DB_CONNECTION', 'sqlite'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => base_path(env('DB_DATABASE', 'storage/database/mailmoll.sqlite')),
            'foreign_key_constraints' => true,
            'wal_mode' => true,
        ],
    ],
];
