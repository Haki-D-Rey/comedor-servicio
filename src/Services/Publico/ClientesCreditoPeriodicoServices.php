<?php

namespace App\Services\Publico;

use App\Repository\Publico\Interface\ClientesCreditoPeriodicoRepositoryInterface;
use App\DTO\Publico\ClientesCreditoPeriodicoDTO;

class ClientesCreditoPeriodicoServices
{
    private ClientesCreditoPeriodicoRepositoryInterface $clienteCreditoPeriodicoRepository;

    public function __construct(ClientesCreditoPeriodicoRepositoryInterface $clienteCreditoPeriodicoRepository)
    {
        $this->clienteCreditoPeriodicoRepository = $clienteCreditoPeriodicoRepository;
    }

    public function getAllClienteCreditoPeriodico(): array
    {
        return $this->clienteCreditoPeriodicoRepository->getAllClienteCreditoPeriodico();
    }

    public function getClienteCreditoPeriodicoById(int $id): ?ClientesCreditoPeriodicoDTO
    {
        return $this->clienteCreditoPeriodicoRepository->getClienteCreditoPeriodicoById($id);
    }

    public function createClienteCreditoPeriodico(array $clienteCreditoPeriodicoDTO): void
    {
        $this->clienteCreditoPeriodicoRepository->createClienteCreditoPeriodico($clienteCreditoPeriodicoDTO);
    }

    public function updateClienteCreditoPeriodico(int $id, ClientesCreditoPeriodicoDTO $clienteCreditoPeriodicoDTO): void
    {
        $this->clienteCreditoPeriodicoRepository->updateClienteCreditoPeriodico($id, $clienteCreditoPeriodicoDTO);
    }

    public function deleteClienteCreditoPeriodico(int $id): void
    {
        $this->clienteCreditoPeriodicoRepository->deleteClienteCreditoPeriodico($id);
    }
}
