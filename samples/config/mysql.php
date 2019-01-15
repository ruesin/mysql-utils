<?php
return [
    'default' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => 'root',
        'password'  => 'root',
        'port'      => '3306'
    ],
    'web' => [
        'online' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'web',
            'username'  => 'web',
            'password'  => 'web',
            'port'      => '3306'
        ],
    ],
    '' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => 'null_user',
        'password'  => 'null_pass',
        'port'      => '3306'
    ],
    '0' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'blog',
        'username'  => '0_user',
        'password'  => '0_pass',
        'port'      => '3306'
    ]
];
