<?php

namespace App\Routes\Catalogo;

use App\Controllers\Catalogo\DepartamentosController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/departamentos', function (RouteCollectorProxy $group) {
        // Rutas estáticas
        $group->get('/all', DepartamentosController ::class . ':getAllDepartamentos')->setName('departamentos.list_all');
    
        // Rutas dinámicas
        $group->get('/{id}', DepartamentosController ::class . ':getDepartamentoById')->setName('departamentos.view');
        $group->post('/', DepartamentosController ::class . ':createDepartamento')->setName('departamentos.create');
        $group->put('/{id}', DepartamentosController ::class . ':updateDepartamento')->setName('departamentos.update');
        $group->delete('/{id}', DepartamentosController ::class . ':deleteDepartamento')->setName('departamentos.delete');
    })->add(AuthMiddleware::class); 
};
