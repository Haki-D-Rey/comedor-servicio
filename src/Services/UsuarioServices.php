<?php

namespace App\Services;

use App\DTO\UsuarioDTO;
use App\Repository\UsuarioRepositoryInterface;

class UsuarioServices
{
    private $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function getAllUsers(): array
    {
        try {
            return $this->usuarioRepository->getAllUsuarios();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getUserById(int $id): ?UsuarioDTO
    {
        try {
            return $this->usuarioRepository->getUsuarioById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createUser(UsuarioDTO $usuarioDTO): void
    {
        try {
            $this->usuarioRepository->createUser($usuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateUser(int $id, UsuarioDTO $usuarioDTO): void
    {
        try {
            $this->usuarioRepository->updateUser($id, $usuarioDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteUser(int $id): void
    {
        try {
            $this->usuarioRepository->deleteUser($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
