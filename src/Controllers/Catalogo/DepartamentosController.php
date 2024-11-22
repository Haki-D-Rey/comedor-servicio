<?php 

namespace APP\Controllers\Catalogo;

use App\DTO\Catalogo\DepartamentosDTO;
use App\Services\Catalogo\DepartamentosServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DepartamentosController
{
    private DepartamentosServices $departamentosServices;

    public function __construct(DepartamentosServices $departamentosServices)
    {
        $this->departamentosServices = $departamentosServices;
    }

    public function getAllDepartamentos(Request $request, Response $response): Response
    {
        try {
            $cargos = $this->departamentosServices->getAllDepartamentos();
            $response->getBody()->write(json_encode($cargos));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getDepartamentoById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $cargos = $this->departamentosServices->getDepartamentoById($id);

            if ($cargos === null) {
                $response->getBody()->write(json_encode(['estado' => false, 'message' => 'Lista de Tipos de Servicios no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($cargos));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createDepartamento(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $departamentosDTO = new DepartamentosDTO(
                null,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->departamentosServices->createDepartamento($departamentosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Tipo de Servicio creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateDepartamento(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $departamentosDTO = new DepartamentosDTO(
                $id,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->departamentosServices->updateDepartamento($id, $departamentosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteDepartamento(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->departamentosServices->deleteDepartamento($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>