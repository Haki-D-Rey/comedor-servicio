<?php

namespace App\Models;

use \PDO;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class DB
{
    public $connections = [];
    public $configs = [];

    public function __construct($databases)
    {
        foreach ($databases as $name => $config) {
            $this->configs[$name] = $config;
            $this->connections[$name] = $this->connect(
                $config['driver'],
                $config['host'],
                $config['user'],
                $config['password'],
                $config['dbname']
            );
        }
    }

    private function connect($driver, $host, $user, $pass, $dbname)
    {
        $dsn = $this->buildDsn($driver, $host, $dbname);
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    private function buildDsn($driver, $host, $dbname)
    {
        switch ($driver) {
            case 'pdo_pgsql':
                return "pgsql:host=$host;dbname=$dbname";
            case 'pdo_mysql':
                return "mysql:host=$host;dbname=$dbname";
            case 'sqlsrv':
                return "dblib:host=$host;dbname=$dbname;charset=UTF-8;";
            default:
                throw new \InvalidArgumentException("Driver '$driver' no es soportado.");
        }
    }

    public function getConnectionConfig($name)
    {
        if (isset($this->connections[$name])) {
            return $this->configs[$name];
        } else {
            throw new \Exception("La conexión '$name' no existe.");
        }
    }
    public function getEntityManager($name)
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../Entity'],
            isDevMode: true
        );

        $connectionConfig = $this->getConnectionConfig($name);

        $connectionParams = [
            'dbname' => $connectionConfig['dbname'],
            'user' => $connectionConfig['user'],
            'password' => $connectionConfig['password'],
            'host' => $connectionConfig['host'],
            'driver' => $connectionConfig['driver']
        ];

        $connection = DriverManager::getConnection($connectionParams);

        return new EntityManager($connection, $config);
    }
}
