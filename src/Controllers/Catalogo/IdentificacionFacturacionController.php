<?php 

namespace APP\Controllers\Catalogo;

use App\DTO\Catalogo\IdentificacionFacturacionDTO;
use App\Services\Catalogo\IdentificacionFacturacionServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IdentificacionFacturacionController
{
    private IdentificacionFacturacionServices $identificacionFacturacionServices;

    public function __construct(IdentificacionFacturacionServices $identificacionFacturacionServices)
    {
        $this->identificacionFacturacionServices = $identificacionFacturacionServices;
    }

    public function getAllIdentificacionFacturacion(Request $request, Response $response): Response
    {
        try {
            $cargos = $this->identificacionFacturacionServices->getAllIdentificacionFacturacion();
            $response->getBody()->write(json_encode($cargos));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getIdentificacionFacturacionById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $cargos = $this->identificacionFacturacionServices->getIdentificacionFacturacionById($id);

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

    public function createIdentificacionFacturacion(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $IdentificacionFacturacionDTO = new IdentificacionFacturacionDTO(
                null,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                new \DateTime(),
                null,
                $data['estado'] ?? true
            );

            $this->identificacionFacturacionServices->createIdentificacionFacturacion($IdentificacionFacturacionDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Tipo de Servicio creado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateIdentificacionFacturacion(Request $request, Response $response, array $args): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $id = (int) $args['id'];

        try {
            $IdentificacionFacturacionDTO = new IdentificacionFacturacionDTO(
                $id,
                $data['nombre'],
                $data['descripcion'],
                $data['codigo_interno'],
                $data['fecha_creacion'] ?? new \DateTime(),
                $data['fecha_modificacion'] ?? new \DateTime(),
                $data['estado'] ?? true
            );

            $this->identificacionFacturacionServices->updateIdentificacionFacturacion($id, $IdentificacionFacturacionDTO);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema actualizado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteIdentificacionFacturacion(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $this->identificacionFacturacionServices->deleteIdentificacionFacturacion($id);
            $response->getBody()->write(json_encode(['estado' => true, 'message' => 'Sistema eliminado exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['estado' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>