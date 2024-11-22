<?php 

namespace APP\Controllers\Catalogo;

use App\DTO\Catalogo\CargosDTO;
use App\Services\Catalogo\CargosServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CargosController
{
    private CargosServices $cargosServices;

    public function __construct(CargosServices $cargosServices)
    {
        $this->cargosServices = $cargosServices;
    }

    public function getAllCargos(Request $request, Response $response): Response
    {
        try {
            $cargos = $this->cargosServices->getAllCargos();
            $response->getBody()->write(json_encode($cargos));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getCargoById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $cargos = $this->cargosServices->getCargoById($id);

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

    public function createCargo(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $CargosDTO = new CargosDTO(
                null,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->cargosServices->createCargo($CargosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Tipo de Servicio creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateCargo(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $CargosDTO = new CargosDTO(
                $id,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->cargosServices->updateCargo($id, $CargosDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteCargo(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->cargosServices->deleteCargo($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>