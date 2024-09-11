<?php

declare(strict_types=1);

use function DI\create;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use App\Controllers\UsuarioController;
use App\Services\DatabaseManager;
use App\Config;
use App\Repository\UsuarioRepository;
use App\Services\UsuarioServices;
use Config\LoggerFactory;
use Doctrine\DBAL\DriverManager;
use Psr\Log\LoggerInterface;

$settings = require __DIR__ . '/../configs/settings.php';

return [
    'settings' => function () use ($settings) {
        return $settings;
    },

    Config::class => create(Config::class)->constructor($_ENV),

    LoggerInterface::class => function () {
        return LoggerFactory::createLogger();
    },

    DatabaseManager::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];
        return new DatabaseManager($settings);
    },

    EntityManager::class => function (ContainerInterface $container) {
        $connectionConfig = $container->get('settings')['db']['dinning_services'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../src/Entity'], // Directorio donde están tus entidades
            true, // Habilitar modo de desarrollo (para caché, etc.)
        );

        $connectionParams = [
            'dbname' => $connectionConfig['dbname'],
            'user' => $connectionConfig['user'],
            'password' => $connectionConfig['password'],
            'host' => $connectionConfig['host'],
            'driver' => $connectionConfig['driver']
        ];

        $connection = DriverManager::getConnection($connectionParams);
        return new EntityManager($connection, $config);
    },

    // Alias para EntityManagerInterface
    EntityManagerInterface::class => DI\get(EntityManager::class),

    UsuarioRepository::class => function (ContainerInterface $container) {
        return new UsuarioRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },
    
    UsuarioServices::class => function (ContainerInterface $container) {
        return new UsuarioServices($container->get(UsuarioRepository::class));
    },

    UsuarioController::class => function (ContainerInterface $container) {
        return new UsuarioController($container->get(UsuarioServices::class));
    },
];
