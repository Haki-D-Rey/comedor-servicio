<?php

namespace App\Services;

use App\DTO\ServiciosProductosDTO;
use App\Repository\ServiciosProductosRepositoryInterface;

class ServiciosProductosServices
{
    private $serviciosProductosRepository;

    public function __construct(ServiciosProductosRepositoryInterface $serviciosProductosRepository)
    {
        $this->serviciosProductosRepository = $serviciosProductosRepository;
    }

    public function getAllServiciosProductos(): array
    {
        try {
            return $this->serviciosProductosRepository->getAllServiciosProductos();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getServiciosProductoById(int $id): ?ServiciosProductosDTO
    {
        try {
            return $this->serviciosProductosRepository->getServiciosProductoById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function createServiciosProducto(ServiciosProductosDTO $serviciosProductosDTO): void
    {
        try {
            $this->serviciosProductosRepository->createServiciosProducto($serviciosProductosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function updateServiciosProducto(int $id, ServiciosProductosDTO $serviciosProductosDTO): void
    {
        try {
            $this->serviciosProductosRepository->updateServiciosProducto($id, $serviciosProductosDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function deleteServiciosProducto(int $id): void
    {
        try {
            $this->serviciosProductosRepository->deleteServiciosProducto($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Servicio del Producto: ' . $e->getMessage());
        }
    }
}
