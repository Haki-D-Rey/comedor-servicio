#!/usr/bin/env php
<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Asegúrate de cargar el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar el contenedor
$container = require __DIR__ . './../configs/container.php';

// Obtener el EntityManager del contenedor
/** @var ContainerInterface $container */
$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

// Crear la consola de Doctrine y asignar el EntityManager
return
    ConsoleRunner::run(
        new SingleManagerProvider($entityManager)
    );
