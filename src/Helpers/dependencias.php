<?php
namespace App\Helpers;

use Psr\Container\ContainerInterface;
use App\Models\UsuarioModel;
use App\Controllers\UsuarioController;
use Doctrine\ORM\EntityManagerInterface;

return [
    'settings' => function (ContainerInterface $container) {
        return $container->get('settings');
    },

    'db' => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];
        return new \App\Models\DB($settings);
    },

    UsuarioModel::class => function (ContainerInterface $container) {
        return new UsuarioModel($container->get(EntityManagerInterface::class));
    },

    UsuarioController::class => function (ContainerInterface $container) {
        return new UsuarioController($container->get(UsuarioModel::class));
    },

    EntityManagerInterface::class => function (ContainerInterface $container) {
        return require __DIR__ . '/../../bootstrap.php';
    },
];
