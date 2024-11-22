<?php

namespace App\Services\Catalogo;

use App\DTO\Catalogo\IdentificacionFacturacionDTO;
use App\Repository\Catalogo\Interface\IdentificacionFacturacionRepositoryInterface;

class IdentificacionFacturacionServices
{
    private $IdentificacionFacturacionRepository;

    public function __construct(IdentificacionFacturacionRepositoryInterface $IdentificacionFacturacionRepository)
    {
        $this->IdentificacionFacturacionRepository = $IdentificacionFacturacionRepository;
    }

    public function getAllIdentificacionFacturacion(): array
    {
        try {
            return $this->IdentificacionFacturacionRepository->getAllIdentificacionFacturacion();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getIdentificacionFacturacionById(int $id): ?IdentificacionFacturacionDTO
    {
        try {
            return $this->IdentificacionFacturacionRepository->getIdentificacionFacturacionById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createIdentificacionFacturacion(IdentificacionFacturacionDTO $identificacionFacturacionDTO): void
    {
        try {
            $this->IdentificacionFacturacionRepository->createIdentificacionFacturacion($identificacionFacturacionDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateIdentificacionFacturacion(int $id, IdentificacionFacturacionDTO $identificacionFacturacionDTO): void
    {
        try {
            $this->IdentificacionFacturacionRepository->updateIdentificacionFacturacion($id, $identificacionFacturacionDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteIdentificacionFacturacion(int $id): void
    {
        try {
            $this->IdentificacionFacturacionRepository->deleteIdentificacionFacturacion($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
