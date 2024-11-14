<?php

namespace App\Services\Catalogo;

use App\DTO\Catalogo\TipoUsuarioDTO;
use App\Repository\Catalogo\Interface\TipoUsuariosRepositoryInterface;
class TipoUsuariosServices
{
    private $tipoUsuariosRepository;

    public function __construct(TipoUsuariosRepositoryInterface $tipoUsuariosRepository)
    {
        $this->tipoUsuariosRepository = $tipoUsuariosRepository;
    }

    public function getAllTipoUsuarios(): array
    {
        try {
            return $this->tipoUsuariosRepository->getAllTipoUsuarios();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getTipoUsuarioById(int $id): ?TipoUsuarioDTO
    {
        try {
            return $this->tipoUsuariosRepository->getTipoUsuarioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createTipoUsuario(TipoUsuarioDTO $TipoUsuarioDTO): void
    {
        try {
            $this->tipoUsuariosRepository->createTipoUsuario($TipoUsuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateTipoUsuario(int $id, TipoUsuarioDTO $TipoUsuarioDTO): void
    {
        try {
            $this->tipoUsuariosRepository->updateTipoUsuario($id, $TipoUsuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteTipoUsuario(int $id): void
    {
        try {
            $this->tipoUsuariosRepository->deleteTipoUsuario($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
