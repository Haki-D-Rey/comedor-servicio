<?php

namespace App\Services;

use App\DTO\TipoServiciosDTO;
use App\Repository\Catalogo\Interface\TipoServiciosRepositoryInterface;
class TipoServiciosServices
{
    private $tipoServiciosRepository;

    public function __construct(TipoServiciosRepositoryInterface $tipoServiciosRepository)
    {
        $this->tipoServiciosRepository = $tipoServiciosRepository;
    }

    public function getAllTipoServicios(): array
    {
        try {
            return $this->tipoServiciosRepository->getAllTipoServicios();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getTipoServicioById(int $id): ?TipoServiciosDTO
    {
        try {
            return $this->tipoServiciosRepository->getTipoServicioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createTipoServicio(TipoServiciosDTO $tipoServiciosDTO): void
    {
        try {
            $this->tipoServiciosRepository->createTipoServicio($tipoServiciosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateTipoServicio(int $id, TipoServiciosDTO $tipoServiciosDTO): void
    {
        try {
            $this->tipoServiciosRepository->updateTipoServicio($id, $tipoServiciosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteTipoServicio(int $id): void
    {
        try {
            $this->tipoServiciosRepository->deleteTipoServicio($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
