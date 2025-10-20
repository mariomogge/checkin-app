<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'mysql-db',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'checkin_db',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: 'root',
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET'),
        'expiration' => 3600
    ],
];
