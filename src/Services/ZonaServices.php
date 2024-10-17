<?php

namespace App\Services;

use App\DTO\ZonaDTO;
use App\Repository\Catalogo\Interface\ZonaRepositoryInterface;

class ZonaServices
{
    private $zonaRepository;

    public function __construct(ZonaRepositoryInterface $zonaRepository)
    {
        $this->zonaRepository = $zonaRepository;
    }

    public function getAllZona(): array
    {
        try {
            return $this->zonaRepository->getAllZona();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getZonaById(int $id): ?ZonaDTO
    {
        try {
            return $this->zonaRepository->getZonaById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createZona(ZonaDTO $zonaDTO): void
    {
        try {
            $this->zonaRepository->createZona($zonaDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateZona(int $id, ZonaDTO $zonaDTO): void
    {
        try {
            $this->zonaRepository->updateZona($id, $zonaDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteZona(int $id): void
    {
        try {
            $this->zonaRepository->deleteZona($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
