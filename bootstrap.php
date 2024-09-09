<?php

use App\Models\DB;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// Cargar autoload.php de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Crear instancia de DB con la configuración de tus bases de datos
$databases = [
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
];
$db = new DB($databases);

// Selecciona la base de datos que deseas utilizar
$selectedDb = 'dinning_services'; // Puedes cambiar esto según sea necesario

// Obtén el EntityManager para la base de datos seleccionada
// $entityManager = $db->getEntityManager($selectedDb);
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/src/Entity'],
    isDevMode: true
);

$connectionConfig = $databases['dinning_services'];

$connectionParams = [
    'dbname' => $connectionConfig['dbname'],
    'user' => $connectionConfig['user'],
    'password' => $connectionConfig['password'],
    'host' => $connectionConfig['host'],
    'driver' => $connectionConfig['driver']
];

$connection = DriverManager::getConnection($connectionParams);

return new EntityManager($connection, $config);
