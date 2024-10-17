<?php

namespace App\DTO\Publico;

use App\Entity\Publico\ControlEstadisticosServicios;
use JsonSerializable;

class ControlEstadisticosServiciosDTO implements JsonSerializable
{
    public ?int $id;
    public string $uuid; // Se asegura que el tipo sea string
    public int $idDetalleZonaServicioHorario; // Cambiar tipo de ConfiguracionServiciosEstadisticos a int
    public int $idConfiguracionServiciosEstadisticos; // Agregar ID para la entidad relacionada
    public int $cantidadFirmada;
    public int $cantidadAnulada;
    public \DateTime $fechaCorte;
    public \DateTime $fechaCreacion;
    public ?\DateTime $fechaModificacion;
    public bool $estado;

    public function __construct(
        ?int $id,
        string $uuid,
        int $idDetalleZonaServicioHorario,
        int $idConfiguracionServiciosEstadisticos, // Asegurar que se pase el ID
        int $cantidadFirmada,
        int $cantidadAnulada,
        \DateTime $fechaCorte,
        \DateTime $fechaCreacion,
        ?\DateTime $fechaModificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->uuid = $uuid; // Aseguramos que sea de tipo string
        $this->idDetalleZonaServicioHorario = $idDetalleZonaServicioHorario;
        $this->idConfiguracionServiciosEstadisticos = $idConfiguracionServiciosEstadisticos; // Inicializar el ID
        $this->cantidadFirmada = $cantidadFirmada;
        $this->cantidadAnulada = $cantidadAnulada;
        $this->fechaCorte = $fechaCorte;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaModificacion = $fechaModificacion;
        $this->estado = $estado;
    }

    // Convert an Entity to DTO
    public static function fromEntity(ControlEstadisticosServicios $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getUuid(), // Convierte el UUID a string
            $entity->getDetalleZonaServicioHorarioId(), // Obtener el ID desde el método que se añadió
            $entity->getConfiguracionServiciosEstadisticosId(), // Obtener el ID de la configuración de servicios estadísticos
            $entity->getCantidadFirmada(),
            $entity->getCantidadAnulada(),
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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getIdDetalleZonaServicioHorario(): int
    {
        return $this->idDetalleZonaServicioHorario;
    }

    public function getIdConfiguracionServiciosEstadisticos(): int
    {
        return $this->idConfiguracionServiciosEstadisticos; // Nuevo getter para el ID de configuración
    }

    public function getCantidadFirmada(): int
    {
        return $this->cantidadFirmada;
    }

    public function getCantidadAnulada(): int
    {
        return $this->cantidadAnulada;
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
            'uuid' => $this->uuid,
            'idDetalleZonaServicioHorario' => $this->idDetalleZonaServicioHorario,
            'idConfiguracionServiciosEstadisticos' => $this->idConfiguracionServiciosEstadisticos,
            'cantidadFirmada' => $this->cantidadFirmada,
            'cantidadAnulada' => $this->cantidadAnulada,
            'fechaCorte' => $this->fechaCorte->format(\DateTime::ISO8601),
            'fechaCreacion' => $this->fechaCreacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fechaModificacion ? $this->fechaModificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
        ];
    }
}
