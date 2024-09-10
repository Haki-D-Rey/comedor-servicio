<?php

namespace App\Repository;

use App\DTO\UsuarioDTO;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class UsuarioRepository extends GenericRepository implements UsuarioRepositoryInterface
{
    private $logger;
    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Usuario::class);
        $this->logger = $loggerInterface;
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
        try {
            $usuario = new Usuario();
            $usuario->setNombreUsuario($usuarioDTO->getNombreUsuario());
            $usuario->setContrasenia($this->hashPassword($usuarioDTO->getContrasenia()));
            $usuario->setNombres($usuarioDTO->getNombres());
            $usuario->setApellidos($usuarioDTO->getApellidos());
            $usuario->setCorreo($usuarioDTO->getCorreo());
            $usuario->setFechaCreacion($usuarioDTO->getFechaCreacion());
            $usuario->setFechaModificacion($usuarioDTO->getFechaModificacion());
            $usuario->setEstado($usuarioDTO->getEstado());

            $this->entityManager->persist($usuario);
            $this->entityManager->flush();

            return true;
        }catch (ORMException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        } catch (DBALException $e) {
            $this->logger->error('Error en la base de datos al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
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

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
