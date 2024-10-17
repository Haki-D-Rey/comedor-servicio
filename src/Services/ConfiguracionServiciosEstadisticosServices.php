<?php

namespace App\Services;

use App\DTO\Publico\ConfiguracionServiciosEstadisticosDTO;
use App\Repository\Publico\Interface\ConfiguracionServiciosEstadisticosRepositoryInterface;

class ConfiguracionServiciosEstadisticosServices
{
    private ConfiguracionServiciosEstadisticosRepositoryInterface $configuracionServiciosEstadisticosRepository;

    public function __construct(ConfiguracionServiciosEstadisticosRepositoryInterface $configuracionServiciosEstadisticosRepository)
    {
        $this->configuracionServiciosEstadisticosRepository = $configuracionServiciosEstadisticosRepository;
    }

    public function getAllConfiguracionServiciosEstadisticos(): array
    {
        try {
            return $this->configuracionServiciosEstadisticosRepository->getAllConfiguracionServiciosEstadisticos();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Control de Estadísticos de Servicios: ' . $e->getMessage());
        }
    }

    public function getControlEstadisticosServiciosById(int $id): ?ConfiguracionServiciosEstadisticosDTO
    {
        try {
            return $this->configuracionServiciosEstadisticosRepository->getConfiguracionServiciosEstadisticosById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Control de Estadísticos de Servicios: ' . $e->getMessage());
        }
    }

    public function createConfiguracionServiciosEstadisticos(ConfiguracionServiciosEstadisticosDTO $configuracionServiciosEstadisticosDTO): ConfiguracionServiciosEstadisticosDTO
    {
        try {
            return $this->configuracionServiciosEstadisticosRepository->createConfiguracionServiciosEstadisticos($configuracionServiciosEstadisticosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Control de Estadísticos de Servicios: ' . $e->getMessage());
        }
    }

    public function updateConfiguracionServiciosEstadisticos(int $id, ConfiguracionServiciosEstadisticosDTO $configuracionServiciosEstadisticosDTO): void
    {
        try {
            $this->configuracionServiciosEstadisticosRepository->updateConfiguracionServiciosEstadisticos($id, $configuracionServiciosEstadisticosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Control de Estadísticos de Servicios: ' . $e->getMessage());
        }
    }

    public function deleteConfiguracionServiciosEstadisticos(int $id): void
    {
        try {
            $this->configuracionServiciosEstadisticosRepository->deleteConfiguracionServiciosEstadisticos($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Control de Estadísticos de Servicios: ' . $e->getMessage());
        }
    }
}
