<?php

namespace App\DTO\Publico;

use App\Entity\Publico\ConfiguracionServiciosEstadisticos;
use JsonSerializable;

class ConfiguracionServiciosEstadisticosDTO implements JsonSerializable
{
    public ?int $id;
    public ?array $jsonConfiguracion;
    public \DateTime $fechaCorte;
    public \DateTime $fechaCreacion;
    public ?\DateTime $fechaModificacion;
    public bool $estado;

    public function __construct(
        ?int $id,
        ?array $jsonConfiguracion,
        \DateTime $fechaCorte,
        \DateTime $fechaCreacion,
        ?\DateTime $fechaModificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->jsonConfiguracion = $jsonConfiguracion;
        $this->fechaCorte = $fechaCorte;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaModificacion = $fechaModificacion;
        $this->estado = $estado;
    }

    // Convert an Entity to DTO
    public static function fromEntity(ConfiguracionServiciosEstadisticos $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getJsonConfiguracion(),
            $entity->getFechaCorte(),
            $entity->getFechaCreacion(),
            $entity->getFechaModificacion(),
            $entity->getEstado()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJsonConfiguracion(): ?array
    {
        return $this->jsonConfiguracion;
    }

    public function getFechaCorte(): \DateTime
    {
        return $this->fechaCorte;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'jsonConfiguracion' => $this->jsonConfiguracion,
            'fechaCorte' => $this -> fechaCorte,
            'fechaCreacion' => $this->fechaCreacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fechaModificacion ? $this->fechaModificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
        ];
    }
}
