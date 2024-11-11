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
use App\Controllers\ConfiguracionServiciosEstadisticosController;
use App\Controllers\ControlEstadisticosServiciosController;
use App\Controllers\DashboardController;
use App\Controllers\DetalleZonaServicioHorarioController;
use App\Controllers\UsuarioController;
use App\Controllers\SistemasController;
use App\Controllers\TipoServiciosController;
use App\Controllers\ServiciosProductosController;
use App\Controllers\ServiciosProductosDetallesController;
use App\Controllers\ZonaController;
use App\Controllers\ZonaUsuariosController;
//Repository
use App\Repository\Catalogo\Repository\DetalleZonaServicioHorarioRepository;
use App\Repository\Catalogo\Repository\SistemasRepository;
use App\Repository\Catalogo\Repository\TipoServiciosRepository;
use App\Repository\Catalogo\Repository\ServiciosProductosRepository;
use App\Repository\Catalogo\Repository\ServiciosProductosDetallesRepository;
use App\Repository\Catalogo\Repository\ZonaRepository;
use App\Repository\Publico\Interface\ConfiguracionServiciosEstadisticosRepositoryInterface;
use App\Repository\Publico\Repository\ConfiguracionServiciosEstadisticosRepository;
use App\Repository\Publico\Repository\ControlEstadisticosServiciosRepository;
use App\Repository\Seguridad\Repository\ZonaUsuarioRepository;
use App\Repository\Seguridad\Repository\AuthRepository;
use App\Repository\Seguridad\Repository\UsuarioRepository;
//Services
use App\Services\AuthServices;
use App\Services\ConfiguracionServiciosEstadisticosServices;
use App\Services\ControlEstadisticosServiciosServices;
use App\Services\DetalleZonaServicioHorarioServices;
use App\Services\UsuarioServices;
use App\Services\SistemasServices;
use App\Services\TipoServiciosServices;
use App\Services\ServiciosProductosServices;
use App\Services\ServiciosProductosDetallesServices;
use App\Services\ZonaServices;
use App\Services\ZonaUsuariosServices;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Yaml\Yaml;

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

        $configFile = __DIR__ . '/package/doctrine.yml';
        $configArray = Yaml::parseFile($configFile);

        if (!Type::hasType('uuid')) {
            Type::addType('uuid', $configArray['doctrine']['types']['uuid']);
        }

        if (!Type::hasType('jsonb')) {
            Type::addType('jsonb', $configArray['doctrine']['types']['jsonb']);
        }
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
            'port' => $connectionConfig['port'],
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

    DetalleZonaServicioHorarioRepository::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    DetalleZonaServicioHorarioServices::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioServices($container->get(DetalleZonaServicioHorarioRepository::class));
    },

    DetalleZonaServicioHorarioController::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioController($container->get(DetalleZonaServicioHorarioServices::class));
    },

    ZonaRepository::class => function (ContainerInterface $container) {
        return new ZonaRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ZonaServices::class => function (ContainerInterface $container) {
        return new ZonaServices($container->get(ZonaRepository::class));
    },

    ZonaController::class => function (ContainerInterface $container) {
        return new ZonaController($container->get(ZonaServices::class));
    },

    ZonaUsuarioRepository::class => function (ContainerInterface $container) {
        return new ZonaUsuarioRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ZonaUsuariosServices::class => function (ContainerInterface $container) {
        return new ZonaUsuariosServices($container->get(ZonaUsuarioRepository::class));
    },

    ZonaUsuariosController::class => function (ContainerInterface $container) {
        return new ZonaUsuariosController($container->get(ZonaUsuariosServices::class));
    },

    //public schema

    ConfiguracionServiciosEstadisticosRepository::class => function (ContainerInterface $container) {
        return new ConfiguracionServiciosEstadisticosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ConfiguracionServiciosEstadisticosServices::class => function (ContainerInterface $container) {
        return new ConfiguracionServiciosEstadisticosServices($container->get(ConfiguracionServiciosEstadisticosRepository::class));
    },

    ConfiguracionServiciosEstadisticosController::class => function (ContainerInterface $container) {
        return new ConfiguracionServiciosEstadisticosController($container->get(ConfiguracionServiciosEstadisticosServices::class));
    },

    ControlEstadisticosServiciosRepository::class => function (ContainerInterface $container) {
        return new ControlEstadisticosServiciosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class), $container->get(ConfiguracionServiciosEstadisticosServices::class));
    },

    ControlEstadisticosServiciosServices::class => function (ContainerInterface $container) {
        return new ControlEstadisticosServiciosServices($container->get(ControlEstadisticosServiciosRepository::class));
    },

    ControlEstadisticosServiciosController::class => function (ContainerInterface $container) {
        return new ControlEstadisticosServiciosController($container->get(ControlEstadisticosServiciosServices::class));
    },

    DashboardController::class => function (ContainerInterface $container) {
        return new DashboardController($container ,$container->get(AuthServices::class));
    },

];
