<?php

return [
    'db' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'c98055.sgvps.net',
            'user' => 'udeq5kxktab81',
            'pass' => 'clmjsfgcrt5m',
            'dbname' => 'db2gdg4nfxpgyk'
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '10.0.30.147',
            'user' => 'postgres',
            'pass' => '&ecurity23',
            'dbname' => 'siservi_catering_local'
        ],
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => 'Dieta',
            'user' => 'sa',
            'pass' => 'PA$$W0RD',
            'dbname' => 'Dieta'
        ],
    ],

    // Otros ajustes globales
    'displayErrorDetails' => true, // Cambia esto a false en producción
    'logErrorDetails' => true, // Cambia esto a false en producción

    // Configuración de JWT (si la usas)
    'jwt' => [
        'secret' => 'your_jwt_secret_key',
        'issuer' => 'your-domain.com',
        'audience' => 'your-domain.com',
        'expiration' => 3600, // Duración del token en segundos (1 hora)
    ],

    // Configuración adicional (por ejemplo, logging, caching, etc.)
    // 'logger' => [
    //     'name' => 'app',
    //     'path' => __DIR__ . '/../logs/app.log',
    // ],
];
