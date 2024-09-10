<?php

namespace App\Repository;

use App\DTO\UsuarioDTO;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioRepository extends GenericRepository implements UsuarioRepositoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Usuario::class);
    }

    public function getAllUsuarios(): array
    {
        $usuarios = $this->getAllEntities();

        return array_map(function (Usuario $usuario) {
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
    }

    public function getUsuarioById(int $id): ?UsuarioDTO
    {
        $usuario = $this->getEntityById($id);

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
        $usuario = $this->getEntityById($id);
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
        $usuario = $this->getEntityById($id);
        if (!$usuario) {
            return false;
        }

        $this->entityManager->remove($usuario);
        $this->entityManager->flush();

        return true;
    }
}
