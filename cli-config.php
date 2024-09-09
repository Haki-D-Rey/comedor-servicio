<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/vendor/autoload.php';

$entityManager = require __DIR__ . '/bootstrap.php';

if (PHP_SAPI === 'cli') {
    ConsoleRunner::run(
        new SingleManagerProvider($entityManager)
    );
}
