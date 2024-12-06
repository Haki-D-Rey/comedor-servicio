<?php

namespace App\Services;

use App\DTO\ZonaUsuarioDTO;
use App\Repository\Seguridad\Interface\ZonaUsuarioRepositoryInterface;

class ZonaUsuariosServices
{
    private $zonaUsuariosRepository;

    public function __construct(ZonaUsuarioRepositoryInterface $zonaUsuariosRepository)
    {
        $this->zonaUsuariosRepository = $zonaUsuariosRepository;
    }

    public function getAllZonaUsuarios(): array
    {
        try {
            return $this->zonaUsuariosRepository->getAllZonaUsuarios();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getRelationalZonaUsuarioById(int $id): array
    {
        try {
            return $this->zonaUsuariosRepository->getRelationalZonaUsuarioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getStadisticZone(object $filtro): array
    {
        try {
            return $this->zonaUsuariosRepository->getStadisticZone($filtro);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function getZonaUsuarioById(int $id): ?ZonaUsuarioDTO
    {
        try {
            return $this->zonaUsuariosRepository->getZonaUsuarioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function createZonaUsuario(array $zonaUsuarioDTO): void
    {
        try {
            $this->zonaUsuariosRepository->createZonaUsuario($zonaUsuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function updateZonaUsuario(int $id, ZonaUsuarioDTO $zonaUsuarioDTO): void
    {
        try {
            $this->zonaUsuariosRepository->updateZonaUsuario($id, $zonaUsuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }

    public function deleteZonaUsuario(int $id): void
    {
        try {
            $this->zonaUsuariosRepository->deleteZonaUsuario($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el Catalogo Detalle del Servicio del Producto: ' . $e->getMessage());
        }
    }
}
