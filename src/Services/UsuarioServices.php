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
        return $this->usuarioRepository->getAllUsuarios();
    }

    public function getUserById(int $id): ?UsuarioDTO
    {
        return $this->usuarioRepository->getUsuarioById($id);
    }

    public function createUser(UsuarioDTO $usuarioDTO): bool
    {
        return $this->usuarioRepository->createUser($usuarioDTO);
    }

    public function updateUser(int $id, UsuarioDTO $usuarioDTO): bool
    {
        return $this->usuarioRepository->updateUser($id, $usuarioDTO);
    }

    public function deleteUser(int $id): bool
    {
        return $this->usuarioRepository->deleteUser($id);
    }

    public function verifyUserPassword(string $usuarioId, string $password): bool
    {
        $usuario = $this->usuarioRepository->getUsuarioById($usuarioId);
        if (!$usuario) {
            return false;
        }
        return $this->usuarioRepository->verifyPassword($password, $usuario->getContrasenia());
    }
}
