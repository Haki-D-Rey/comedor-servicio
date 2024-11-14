#!/usr/bin/env php
<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\Tools\Console\ConsoleRunner as MigrationsConsoleRunner;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar el contenedor
$container = require __DIR__ . './../configs/container.php';

/** @var ContainerInterface $container */
$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

$config = new YamlFile(__DIR__ . '/../configs/package/migrations.yml');

$dependencyFactory = DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));

$commandType = getenv('DOCTRINE_COMMAND_TYPE');

if ($commandType === 'migrations') {
    return MigrationsConsoleRunner::run(
        [],
        $dependencyFactory
    );
} else {
    return ConsoleRunner::run(
        new SingleManagerProvider($entityManager)
    );
}
