<?php

use Psr\Container\ContainerInterface;
use App\Models\DB;
use App\Models\User;
use App\Models\RevokedTokens;

return [
 'settings' => function (ContainerInterface $container) {
        return $container->get('settings');
    },

    'db' => function (ContainerInterface $container) {
        // Obtener la configuración de la base de datos del contenedor
        $settings = $container->get('settings')['db'];
        return new DB($settings);
    },

    'userModel' => function (ContainerInterface $container) {
        // Obtener la instancia de la clase DB del contenedor
        $db = $container->get('db');
        // Aquí puedes especificar la conexión que deseas usar, por ejemplo, 'mysql', 'pgsql' o 'sqlsrv'
        return new User($db, 'mysql'); // Cambia 'mysql' a 'pgsql' o 'sqlsrv' según sea necesario
    },

    'revokedTokensModel' => function (ContainerInterface $container) {
        // Obtener la instancia de la clase DB del contenedor
        $db = $container->get('db');
        // Aquí puedes especificar la conexión que deseas usar, por ejemplo, 'mysql', 'pgsql' o 'sqlsrv'
        return new RevokedTokens($db, 'mysql'); // Cambia 'mysql' a 'pgsql' o 'sqlsrv' según sea necesario
    },
];
