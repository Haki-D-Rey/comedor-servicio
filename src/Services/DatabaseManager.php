<?php

namespace App\Services;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class DatabaseManager
{
    private array $connections = [];
    private array $configs;

    public function __construct(array $databases)
    {
        $this->configs = $databases;
    }

    public function getEntityManager(string $name): EntityManager
    {
        if (!isset($this->configs[$name])) {
            throw new \Exception("La configuración para '$name' no existe.");
        }

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../Entity'],
            isDevMode: true
        );

        $connectionParams = $this->configs[$name];

        $connection = DriverManager::getConnection($connectionParams);

        return new EntityManager($connection, $config);
    }
}
?>