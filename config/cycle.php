<?php

use Cycle\ORM\Schema;
use Cycle\ORM\Select\Source;
use Pandawa\Cycle\Repository\Repository;

return [
    'directories' => [
        base_path('src/*/*/*/Entity/'),
    ],
    'exclude'     => [],
    'scopes'      => [],

    'schema' => [
        'cache'         => [
            'storage' => env('CYCLE_SCHEMA_CACHE_STORAGE'),
            'enabled' => env('CYCLE_SCHEMA_CACHE_ENABLED', false),
        ],
        'defaults'      => [
            Schema::DATABASE   => 'default',
            Schema::SOURCE     => Source::class,
            Schema::REPOSITORY => Repository::class,
        ],
        'resource_path' => base_path('src/*/*/*/Resources/schemas'),
    ],
];
