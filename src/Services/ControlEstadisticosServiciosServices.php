<?php

namespace App\Services;

use App\DTO\Publico\ControlEstadisticosServiciosDTO;
use App\Repository\Publico\Interface\ControlEstadisticosServiciosRepositoryInterface;

class ControlEstadisticosServiciosServices
{
    private ControlEstadisticosServiciosRepositoryInterface $controlEstadisticosServiciosRepository;

    public function __construct(ControlEstadisticosServiciosRepositoryInterface $controlEstadisticosServiciosRepository)
    {
        $this->controlEstadisticosServiciosRepository = $controlEstadisticosServiciosRepository;
    }

    public function getAllControlEstadisticosServicios(): array
    {
        try {
            return $this->controlEstadisticosServiciosRepository->getAllControlEstadisticosServicios();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function getControlEstadisticosServiciosById(int $id): ?ControlEstadisticosServiciosDTO
    {
        try {
            return $this->controlEstadisticosServiciosRepository->getControlEstadisticosServiciosById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function getControlEstadisticosServiciosByIdForDate(string $date): ?array
    {
        try {
            return $this->controlEstadisticosServiciosRepository->getControlEstadisticosServiciosByIdForDate($date);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function createFormControlEstadisticosServicios(array $configuracionServiciosEstadisticos): void
    {
        try {
            $this->controlEstadisticosServiciosRepository->createFormControlEstadisticosServicios($configuracionServiciosEstadisticos);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function createControlEstadisticosServicios(ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void
    {
        try {
            $this->controlEstadisticosServiciosRepository->createControlEstadisticosServicios($controlEstadisticosServiciosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function updateControlEstadisticosServicios(int $id, ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void
    {
        try {
            $this->controlEstadisticosServiciosRepository->updateControlEstadisticosServicios($id, $controlEstadisticosServiciosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function deleteControlEstadisticosServicios(int $id): void
    {
        try {
            $this->controlEstadisticosServiciosRepository->deleteControlEstadisticosServicios($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Control de Estadísticos de Servicios: ' . $e->getMessage(), $e->getCode());
        }
    }
}
