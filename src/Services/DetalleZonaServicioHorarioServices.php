<?php

namespace App\Services;

use App\DTO\DetalleZonasServicioHorarioDTO;
use App\Repository\Catalogo\Interface\DetalleZonaServicioHorarioRepositoryInterface;

class DetalleZonaServicioHorarioServices
{
    private $detalleZonaServicioHorarioRepository;

    public function __construct(DetalleZonaServicioHorarioRepositoryInterface $detalleZonaServicioHorarioRepository)
    {
        $this->detalleZonaServicioHorarioRepository = $detalleZonaServicioHorarioRepository;
    }

    public function getAllDetalleZonasServicioHorario(): array
    {
        try {
            return $this->detalleZonaServicioHorarioRepository->getAllDetalleZonasServicioHorario();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getAllDetalleZonaServicioHorarioByZonaUsuario($id): array
    {
        try {
            return $this->detalleZonaServicioHorarioRepository->getAllDetalleZonaServicioHorarioByZonaUsuario($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }


    public function getAllDetalleZonaServicioHorarioByIdZonaUsuario($id): array
    {
        try {
            return $this->detalleZonaServicioHorarioRepository->getAllDetalleZonaServicioHorarioByIdZonaUsuario($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getDetalleZonasServicioHorarioById(int $id): ?DetalleZonasServicioHorarioDTO
    {
        try {
            return $this->detalleZonaServicioHorarioRepository->getDetalleZonasServicioHorarioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function createDetalleZonasServicioHorario(array $detalleZonasServicioHorarioDTO): void
    {
        try {
            $this->detalleZonaServicioHorarioRepository->createDetalleZonasServicioHorario($detalleZonasServicioHorarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function updateDetalleZonasServicioHorario(int $id, DetalleZonasServicioHorarioDTO $detalleZonasServicioHorarioDTO): void
    {
        try {
            $this->detalleZonaServicioHorarioRepository->updateDetalleZonasServicioHorario($id, $detalleZonasServicioHorarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function deleteDetalleZonasServicioHorario(int $id): void
    {
        try {
            $this->detalleZonaServicioHorarioRepository->deleteDetalleZonasServicioHorario($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }
}
