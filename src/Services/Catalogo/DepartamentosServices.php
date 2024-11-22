<?php

namespace App\Services\Catalogo;

use App\DTO\Catalogo\DepartamentosDTO;
use App\Repository\Catalogo\Interface\DepartamentosRepositoryInterface;

class DepartamentosServices
{
    private $departamentoRepository;

    public function __construct(DepartamentosRepositoryInterface $departamentoRepository)
    {
        $this->departamentoRepository = $departamentoRepository;
    }

    public function getAllDepartamentos(): array
    {
        try {
            return $this->departamentoRepository->getAllDepartamentos();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    public function getDepartamentoById(int $id): ?DepartamentosDTO
    {
        try {
            return $this->departamentoRepository->getDepartamentoById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    public function createDepartamento(DepartamentosDTO $cargoDTO): void
    {
        try {
            $this->departamentoRepository->createDepartamento($cargoDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateDepartamento(int $id, DepartamentosDTO $cargoDTO): void
    {
        try {
            $this->departamentoRepository->updateDepartamento($id, $cargoDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteDepartamento(int $id): void
    {
        try {
            $this->departamentoRepository->deleteDepartamento($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
