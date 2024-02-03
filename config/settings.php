<?php

use Psr\Container\ContainerInterface;

// phpcs:disable PSR1.Files.SideEffects

define('APP_ROOT', realpath(__DIR__ . '/../'));

return [
    'settings' => [
        'slim'     => [
        ],
        'doctrine' => [
            'dev_mode'      => true,
            'cache_dir'     => APP_ROOT . '/var/doctrine',
            'metadata_dirs' => [ APP_ROOT . '/src/App/Domain' ],
            'connection'    => [
                'driver' => 'pdo_sqlite',
                'path'   => APP_ROOT . '/data/db.sqlite',
            ]
        ]
    ],
];
