<?php

declare(strict_types=1);

use function DI\create;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use App\Services\DatabaseManager;
use App\Config;
use Config\LoggerFactory;
use Doctrine\DBAL\DriverManager;
use Psr\Log\LoggerInterface;

//Controller
use App\Controllers\AuthController;
use App\Controllers\UsuarioController;
use App\Controllers\SistemasController;
use App\Controllers\TipoServiciosController;
use App\Controllers\ServiciosProductosController;
use App\Controllers\ServiciosProductosDetallesController;

//Repository
use App\Repository\AuthRepository;
use App\Repository\UsuarioRepository;
use App\Repository\SistemasRepository;
use App\Repository\TipoServiciosRepository;
use App\Repository\ServiciosProductosRepository;
use App\Repository\ServiciosProductosDetallesRepository;

//Services
use App\Services\AuthServices;
use App\Services\UsuarioServices;
use App\Services\SistemasServices;
use App\Services\TipoServiciosServices;
use App\Services\ServiciosProductosServices;
use App\Services\ServiciosProductosDetallesServices;

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

    AuthRepository::class => function (ContainerInterface $container) {
        return new AuthRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class), $container);
    },

    AuthServices::class => function (ContainerInterface $container) {
        return new AuthServices($container->get(AuthRepository::class));
    },

    AuthController::class => function (ContainerInterface $container) {
        return new AuthController($container->get(AuthServices::class), $container);
    },

    SistemasRepository::class => function (ContainerInterface $container) {
        return new SistemasRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    SistemasServices::class => function (ContainerInterface $container) {
        return new SistemasServices($container->get(SistemasRepository::class));
    },

    SistemasController::class => function (ContainerInterface $container) {
        return new SistemasController($container->get(SistemasServices::class));
    },

    TipoServiciosRepository::class => function (ContainerInterface $container) {
        return new TipoServiciosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    TipoServiciosServices::class => function (ContainerInterface $container) {
        return new TipoServiciosServices($container->get(TipoServiciosRepository::class));
    },

    TipoServiciosController::class => function (ContainerInterface $container) {
        return new TipoServiciosController($container->get(TipoServiciosServices::class));
    },

    ServiciosProductosRepository::class => function (ContainerInterface $container) {
        return new ServiciosProductosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ServiciosProductosServices::class => function (ContainerInterface $container) {
        return new ServiciosProductosServices($container->get(ServiciosProductosRepository::class));
    },

    ServiciosProductosController::class => function (ContainerInterface $container) {
        return new ServiciosProductosController($container->get(ServiciosProductosServices::class));
    },

    ServiciosProductosDetallesRepository::class => function (ContainerInterface $container) {
        return new ServiciosProductosDetallesRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ServiciosProductosDetallesServices::class => function (ContainerInterface $container) {
        return new ServiciosProductosDetallesServices($container->get(ServiciosProductosDetallesRepository::class));
    },

    ServiciosProductosDetallesController::class => function (ContainerInterface $container) {
        return new ServiciosProductosDetallesController($container->get(ServiciosProductosDetallesServices::class));
    },

];
