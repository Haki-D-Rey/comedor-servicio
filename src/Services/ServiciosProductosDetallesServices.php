<?php

namespace App\Services;

use App\DTO\ServiciosProductosDetallesDTO;
use App\Repository\Catalogo\Interface\ServiciosProductosDetallesRepositoryInterface;
class ServiciosProductosDetallesServices
{
    private $serviciosProductosDetallesRepository;

    public function __construct(ServiciosProductosDetallesRepositoryInterface $serviciosProductosDetallesRepository)
    {
        $this->serviciosProductosDetallesRepository = $serviciosProductosDetallesRepository;
    }

    public function getAllServiciosProductosDetalles(): array
    {
        try {
            return $this->serviciosProductosDetallesRepository->getAllServiciosProductosDetalles();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getServiciosProductosDetallesById(int $id): ?ServiciosProductosDetallesDTO
    {
        try {
            return $this->serviciosProductosDetallesRepository->getServiciosProductosDetallesById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function createServiciosProductosDetalles(ServiciosProductosDetallesDTO $serviciosProductosDTO): void
    {
        try {
            $this->serviciosProductosDetallesRepository->createServiciosProductosDetalles($serviciosProductosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function updateServiciosProductosDetalles(int $id, ServiciosProductosDetallesDTO $serviciosProductosDTO): void
    {
        try {
            $this->serviciosProductosDetallesRepository->updateServiciosProductosDetalles($id, $serviciosProductosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function deleteServiciosProductosDetalles(int $id): void
    {
        try {
            $this->serviciosProductosDetallesRepository->deleteServiciosProductosDetalles($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }
}
