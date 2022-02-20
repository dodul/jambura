<?php
include('configurations.php');
return
[
    'paths' => [
        'migrations' => 'migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => DB_SERVER,
            'name' => DB_NAME,
            'user' => DB_SERVER_USERNAME,
            'pass' => DB_SERVER_PASSWORD,
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => DB_SERVER,
            'name' => DB_NAME,
            'user' => DB_SERVER_USERNAME,
            'pass' => DB_SERVER_PASSWORD,
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => DB_SERVER,
            'name' => DB_NAME,
            'user' => DB_SERVER_USERNAME,
            'pass' => DB_SERVER_PASSWORD,
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
