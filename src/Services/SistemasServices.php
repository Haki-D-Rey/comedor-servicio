<?php

namespace App\Services;

use App\DTO\SistemasDTO;
use App\Repository\Catalogo\Interface\SistemasRepositoryInterface;

class SistemasServices
{
    private $sistemasRepository;

    public function __construct(SistemasRepositoryInterface $sistemasRepository)
    {
        $this->sistemasRepository = $sistemasRepository;
    }

    public function getAllSistemas(): array
    {
        try {
            return $this->sistemasRepository->getAllSistemas();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getSistemaById(int $id): ?SistemasDTO
    {
        try {
            return $this->sistemasRepository->getSistemaById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createSistema(SistemasDTO $sistemasDTO): void
    {
        try {
            $this->sistemasRepository->createSistema($sistemasDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updatesistema(int $id, SistemasDTO $sistemasDTO): void
    {
        try {
            $this->sistemasRepository->updateSistema($id, $sistemasDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteSistema(int $id): void
    {
        try {
            $this->sistemasRepository->deleteSistema($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
