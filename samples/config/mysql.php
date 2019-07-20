<?php
return [
    'default' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => 'root',
        'password'  => 'root',
        'port'      => '3306',
        'retry_exception' => [
            // 'MySQL server has gone away'
        ]
    ],
    'web' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'web',
        'username'  => 'root',
        'password'  => 'root',
        'port'      => '3306'
    ],
    '' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => 'root',
        'password'  => 'root',
        'port'      => '3306'
    ],
    '0' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => 'root',
        'password'  => 'root',
        'port'      => '3306'
    ]
];
