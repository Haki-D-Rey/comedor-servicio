<?php 
return [
    'db' => [
        'mysql' => [
            'driver' => 'pdo_mysql',
            'host' => 'c98055.sgvps.net',
            'user' => 'uj6fjylfypd74',
            'password' => 'lzrfvyapldaw',
            'dbname' => 'db2gdg4nfxpgyk'
        ],
        'pgsql' => [
            'driver' => 'pdo_pgsql',
            'host' => 'c98055.sgvps.net',
            'user' => 'umao144lpzdd3',
            'password' => 'clmjsfgcrt5m',
            'dbname' => 'db6fq3nnewvjrs'
        ],
        'dinning_services' => [
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'user' => 'postgres',
            'password' => 'n&ecurity2024*',
            'dbname' => 'dining_service'
        ],
    ],
    
    // Otros ajustes globales
    'displayErrorDetails' => true,
    'logErrorDetails' => true,

    'jwt' => [
        'secret' => 'haki12345',
        'issuer' => 'your-domain.com',
        'audience' => 'your-domain.com',
        'expiration' => 3600,
    ],
];

?>