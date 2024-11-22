<?php

namespace App\Services\Catalogo;

use App\DTO\Catalogo\CargosDTO;
use App\Repository\Catalogo\Interface\CargosRepositoryInterface;

class CargosServices
{
    private $cargoRepository;

    public function __construct(CargosRepositoryInterface $cargoRepository)
    {
        $this->cargoRepository = $cargoRepository;
    }

    public function getAllCargos(): array
    {
        try {
            return $this->cargoRepository->getAllCargos();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getCargoById(int $id): ?CargosDTO
    {
        try {
            return $this->cargoRepository->getCargoById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createCargo(CargosDTO $cargoDTO): void
    {
        try {
            $this->cargoRepository->createCargo($cargoDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateCargo(int $id, CargosDTO $cargoDTO): void
    {
        try {
            $this->cargoRepository->updateCargo($id, $cargoDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteCargo(int $id): void
    {
        try {
            $this->cargoRepository->deleteCargo($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
