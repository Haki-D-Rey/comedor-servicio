<?php

namespace App\Models;

use App\Entity\Usuario;
use App\DTO\UsuarioDTO;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioModel
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllUsers(): ?array
    {
        $query = $this->entityManager->createQuery('SELECT u FROM App\Entity\Usuario u');
        $usuarios = $query->getResult();
        
        $usuarioDTOs = array_map(function (Usuario $usuario) {
            return new UsuarioDTO(
                $usuario->getId(),
                $usuario->getNombreUsuario(),
                $usuario->getContrasenia(),
                $usuario->getNombres(),
                $usuario->getApellidos(),
                $usuario->getCorreo(),
                $usuario->getFechaCreacion(),
                $usuario->getFechaModificacion(),
                $usuario->getEstado()
            );
        }, $usuarios);
        
        return $usuarioDTOs;
    }

    public function getUserById(int $id): ?UsuarioDTO
    {
        $usuario = $this->entityManager->find(Usuario::class, $id);
        if (!$usuario) {
            return null;
        }

        return new UsuarioDTO(
            $usuario->getId(),
            $usuario->getNombreUsuario(),
            $usuario->getContrasenia(),
            $usuario->getNombres(),
            $usuario->getApellidos(),
            $usuario->getCorreo(),
            $usuario->getFechaCreacion(),
            $usuario->getFechaModificacion(),
            $usuario->getEstado()
        );
    }

    public function createUser(UsuarioDTO $usuarioDTO): bool
    {
        $usuario = new Usuario();
        $usuario->setNombreUsuario($usuarioDTO->getNombreUsuario());
        $usuario->setContrasenia($usuarioDTO->getContrasenia());
        $usuario->setNombres($usuarioDTO->getNombres());
        $usuario->setApellidos($usuarioDTO->getApellidos());
        $usuario->setCorreo($usuarioDTO->getCorreo());
        $usuario->setFechaCreacion($usuarioDTO->getFechaCreacion());
        $usuario->setFechaModificacion($usuarioDTO->getFechaModificacion());
        $usuario->setEstado($usuarioDTO->getEstado());

        $this->entityManager->persist($usuario);
        $this->entityManager->flush();

        return true;
    }

    public function updateUser(int $id, UsuarioDTO $usuarioDTO): bool
    {
        $usuario = $this->entityManager->find(Usuario::class, $id);
        if (!$usuario) {
            return false;
        }

        $usuario->setNombreUsuario($usuarioDTO->getNombreUsuario());
        $usuario->setContrasenia($usuarioDTO->getContrasenia());
        $usuario->setNombres($usuarioDTO->getNombres());
        $usuario->setApellidos($usuarioDTO->getApellidos());
        $usuario->setCorreo($usuarioDTO->getCorreo());
        $usuario->setFechaModificacion($usuarioDTO->getFechaModificacion());
        $usuario->setEstado($usuarioDTO->getEstado());

        $this->entityManager->flush();

        return true;
    }

    public function deleteUser(int $id): bool
    {
        $usuario = $this->entityManager->find(Usuario::class, $id);
        if (!$usuario) {
            return false;
        }

        $this->entityManager->remove($usuario);
        $this->entityManager->flush();

        return true;
    }
}
