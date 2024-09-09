<?php

namespace App\Controllers;

use App\Models\DB;
use Slim\Psr7\Response;
use Slim\Psr7\Request;

use PHPMailer\PHPMailer\PHPMailer;

class ApiController
{
    protected PHPMailer $mailServer;
    protected $databases = [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'c98055.sgvps.net',
            'user' => 'udeq5kxktab81',
            'pass' => 'clmjsfgcrt5m',
            'dbname' => 'db2gdg4nfxpgyk'
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'c98055.sgvps.net',
            'user' => 'umao144lpzdd3',
            'pass' => 'clmjsfgcrt5m',
            'dbname' => 'db6fq3nnewvjrs'
        ],
        'dinning_services' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'user' => 'postgres',
            'pass' => 'n&ecurity2024*',
            'dbname' => 'dining_service'
        ],
    ];

    public function index(Request $request, Response $response): Response
    {
        ob_start();
    
        include __DIR__ . '/../../public/views/client/inicio.php';
    
        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);

        return $response->withHeader('Content-Type', 'text/html');
    }

    public function formulario(Request $request, Response $response): Response
    {
        ob_start();
    
        include __DIR__ . '/../../public/views/client/formulario/inscripcion_control.php';
    
        $viewContent = ob_get_clean();
        $response->getBody()->write($viewContent);
    
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function info(Request $request, Response $response): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        $response->getBody()->write($phpinfo); // Escribe el contenido en la respuesta
        return $response->withHeader('Content-Type', 'text/html'); // Establece el tipo de contenido como HTML
    }
    

    public function getConnection()
    {
        $multiDB = new DB($this->databases);

        try {

            var_dump($multiDB->connections);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    
}
