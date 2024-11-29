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
use Doctrine\ORM\Events;
use App\Helpers\EntityLoadListener;

//Controller
use App\Controllers\AuthController;
use APP\Controllers\Catalogo\CargosController;
use APP\Controllers\Catalogo\DepartamentosController;
use APP\Controllers\Catalogo\IdentificacionFacturacionController;
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
use App\Controllers\Catalogo\TipoUsuariosController;
use App\Controllers\Publico\ClientesCreditoPeriodicoController;
use App\Controllers\Publico\ClientesController;
use App\Controllers\Publico\DetalleClienteIdentificacionFacturacionController;
use App\Controllers\Publico\DetalleZonaServicioHorarioClienteFacturacionController;
use App\Controllers\Publico\VentasController;
use App\Controllers\Test\PadreController;
use App\Middlewares\AuthorizationMiddleware;
use App\Repository\Catalogo\Repository\CargosRepository;
use App\Repository\Catalogo\Repository\DepartamentosRepository;
//Repository
use App\Repository\Catalogo\Repository\DetalleZonaServicioHorarioRepository;
use App\Repository\Catalogo\Repository\IdentificacionFacturacionRepository;
use App\Repository\Catalogo\Repository\SistemasRepository;
use App\Repository\Catalogo\Repository\TipoServiciosRepository;
use App\Repository\Catalogo\Repository\ServiciosProductosRepository;
use App\Repository\Catalogo\Repository\ServiciosProductosDetallesRepository;
use App\Repository\Catalogo\Repository\TipoUsuariosRepository;
use App\Repository\Catalogo\Repository\ZonaRepository;
use App\Repository\Publico\Repository\ClientesCreditoPeriodicoRepository;
use App\Repository\Publico\Repository\ClientesRepository;
use App\Repository\Publico\Repository\ConfiguracionServiciosEstadisticosRepository;
use App\Repository\Publico\Repository\ControlEstadisticosServiciosRepository;
use App\Repository\Publico\Repository\DetalleClienteIdentificacionFacturacionRepository;
use App\Repository\Publico\Repository\DetalleZonaServicioHorarioClienteFacturacionRepository;
use App\Repository\Publico\Repository\VentasRepository;
use App\Repository\Seguridad\Repository\ZonaUsuarioRepository;
use App\Repository\Seguridad\Repository\AuthRepository;
use App\Repository\Seguridad\Repository\UsuarioRepository;
use App\Services\Seguridad\AuthorizationService;
//Services
use App\Services\AuthServices;
use App\Services\Catalogo\CargosServices;
use App\Services\Catalogo\DepartamentosServices;
use App\Services\Catalogo\IdentificacionFacturacionServices;
use App\Services\Catalogo\TipoUsuariosServices;
use App\Services\ConfiguracionServiciosEstadisticosServices;
use App\Services\ControlEstadisticosServiciosServices;
use App\Services\DetalleZonaServicioHorarioServices;
use App\Services\Publico\ClientesCreditoPeriodicoServices;
use App\Services\Publico\ClientesServices;
use App\Services\Publico\DetalleClienteIdentificacionFacturacionServices;
use App\Services\Publico\DetalleZonaServicioHorarioClienteFacturacionServices;
use App\Services\Publico\VentasServices;
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

        // Configuración de ORM con opciones de proxy
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../src/Entity'],
            false,
        );

        $config->setProxyDir(__DIR__ . '/../bin/cache/doctrine/proxies');
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(false); // true en desarrollo

        $connectionParams = [
            'dbname' => $connectionConfig['dbname'],
            'user' => $connectionConfig['user'],
            'password' => $connectionConfig['password'],
            'host' => $connectionConfig['host'],
            'port' => $connectionConfig['port'],
            'driver' => $connectionConfig['driver']
        ];

        $connection = DriverManager::getConnection($connectionParams);
        $entityManager = new EntityManager($connection, $config);

        //Añadir el listener para el evento `postLoad`
        $entityManager->getEventManager()->addEventListener(
            [Events::postLoad],
            new EntityLoadListener()
        );

        return $entityManager;
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

    TipoUsuariosRepository::class => function (ContainerInterface $container) {
        return new TipoUsuariosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    TipoUsuariosServices::class => function (ContainerInterface $container) {
        return new TipoUsuariosServices($container->get(TipoUsuariosRepository::class));
    },

    TipoUsuariosController::class => function (ContainerInterface $container) {
        return new TipoUsuariosController($container->get(TipoUsuariosServices::class));
    },

    //FLUJO DE CATALOGO CARGOS
    CargosRepository::class => function (ContainerInterface $container) {
        return new CargosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    CargosServices::class => function (ContainerInterface $container) {
        return new CargosServices($container->get(CargosRepository::class));
    },

    CargosController::class => function (ContainerInterface $container) {
        return new CargosController($container->get(CargosServices::class));
    },

    //FLUJO DE CATALOGO DEPARTAMENTOS
    DepartamentosRepository::class => function (ContainerInterface $container) {
        return new DepartamentosRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    DepartamentosServices::class => function (ContainerInterface $container) {
        return new DepartamentosServices($container->get(DepartamentosRepository::class));
    },

    DepartamentosController::class => function (ContainerInterface $container) {
        return new DepartamentosController($container->get(DepartamentosServices::class));
    },

    //FLUJO DE CATALOGO IDENTIFICACIONFACTURACION
    IdentificacionFacturacionRepository::class => function (ContainerInterface $container) {
        return new IdentificacionFacturacionRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    IdentificacionFacturacionServices::class => function (ContainerInterface $container) {
        return new IdentificacionFacturacionServices($container->get(IdentificacionFacturacionRepository::class));
    },

    IdentificacionFacturacionController::class => function (ContainerInterface $container) {
        return new IdentificacionFacturacionController($container->get(IdentificacionFacturacionServices::class));
    },

    //FLUJO DE CATALOGO DETALLE CLIENTE IDENTIFICACION FACTURACION
    DetalleClienteIdentificacionFacturacionRepository::class => function (ContainerInterface $container) {
        return new DetalleClienteIdentificacionFacturacionRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    DetalleClienteIdentificacionFacturacionServices::class => function (ContainerInterface $container) {
        return new DetalleClienteIdentificacionFacturacionServices($container->get(DetalleClienteIdentificacionFacturacionRepository::class));
    },

    DetalleClienteIdentificacionFacturacionController::class => function (ContainerInterface $container) {
        return new DetalleClienteIdentificacionFacturacionController($container->get(DetalleClienteIdentificacionFacturacionServices::class));
    },

    //FLUJO DE CATALOGO DETALLE ZONA SERVICIO HORARIO CLIENTE IDENTIFICACION FACTURACION
    DetalleZonaServicioHorarioClienteFacturacionRepository::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioClienteFacturacionRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    DetalleZonaServicioHorarioClienteFacturacionServices::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioClienteFacturacionServices($container->get(DetalleZonaServicioHorarioClienteFacturacionRepository::class));
    },

    DetalleZonaServicioHorarioClienteFacturacionController::class => function (ContainerInterface $container) {
        return new DetalleZonaServicioHorarioClienteFacturacionController($container->get(DetalleZonaServicioHorarioClienteFacturacionServices::class));
    },

    //FLUJO DE TRABAJO DE CLIENTE CREDITO PERIODICO POR EVENTO
    ClientesCreditoPeriodicoRepository::class => function (ContainerInterface $container) {
        return new ClientesCreditoPeriodicoRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class));
    },

    ClientesCreditoPeriodicoServices::class => function (ContainerInterface $container) {
        return new ClientesCreditoPeriodicoServices($container->get(ClientesCreditoPeriodicoRepository::class));
    },

    ClientesCreditoPeriodicoController::class => function (ContainerInterface $container) {
        return new ClientesCreditoPeriodicoController($container->get(ClientesCreditoPeriodicoServices::class));
    },

    PadreController::class => function (ContainerInterface $container) {
        return new PadreController($container->get(EntityManagerInterface::class));
    },

    //FLUJO DE PERMISOS POR CADA VISTAS O ACCESOS
    AuthorizationService::class => function (ContainerInterface $container) {
        return new AuthorizationService($container->get(EntityManagerInterface::class));
    },

    AuthorizationMiddleware::class => function (ContainerInterface $container) {
        return new AuthorizationMiddleware($container->get(AuthorizationService::class), $container->get(EntityManagerInterface::class));
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

    //FLUJO DE TRABAJO CLIENTES
    ClientesRepository::class => function (ContainerInterface $container) {
        return new ClientesRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class), $container->get(ConfiguracionServiciosEstadisticosServices::class));
    },

    ClientesServices::class => function (ContainerInterface $container) {
        return new ClientesServices($container->get(ClientesRepository::class));
    },

    ClientesController::class => function (ContainerInterface $container) {
        return new ClientesController($container->get(ClientesServices::class));
    },

    //FLUJO DE TRABAJO VENTAS
    VentasRepository::class => function (ContainerInterface $container) {
        return new VentasRepository($container->get(EntityManagerInterface::class), $container->get(LoggerInterface::class), $container->get(ConfiguracionServiciosEstadisticosServices::class));
    },

    VentasServices::class => function (ContainerInterface $container) {
        return new VentasServices($container->get(VentasRepository::class));
    },

    VentasController::class => function (ContainerInterface $container) {
        return new VentasController($container, $container->get(VentasServices::class),  $container->get(AuthServices::class));
    },


    DashboardController::class => function (ContainerInterface $container) {
        return new DashboardController($container, $container->get(AuthServices::class));
    },

];
