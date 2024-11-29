<?php

namespace App\DTO\Publico;

use App\Entity\Publico\Ventas;
use JsonSerializable;

class VentasDTO implements JsonSerializable
{
    public ?int $id;
    public string $uuid; // Ensures UUID is handled as a string
    public int $idDetalleZonaServicioHorarioClienteFacturacion; // Foreign key to the related entity
    public int $idCliente;
    public int $cantidadFacturada;
    public bool $ticketAnulado;
    public int $cantidadAnulada;
    public \DateTime $fechaEmision;
    public ?\DateTime $fechaModificacion;
    public int $idEstadoVenta; // Foreign key for estado_venta
    public bool $estado;

    public function __construct(
        ?int $id,
        string $uuid,
        int $idDetalleZonaServicioHorarioClienteFacturacion,
        int $idCliente,
        int $cantidadFacturada,
        bool $ticketAnulado,
        int $cantidadAnulada,
        \DateTime $fechaEmision,
        ?\DateTime $fechaModificacion,
        int $idEstadoVenta,
        bool $estado
    ) {
        $this->id = $id;
        $this->uuid = 'TICKET-' . $uuid; // UUID should be passed as a string
        $this->idDetalleZonaServicioHorarioClienteFacturacion = $idDetalleZonaServicioHorarioClienteFacturacion;
        $this->idCliente = $idCliente;
        $this->cantidadFacturada = $cantidadFacturada;
        $this->ticketAnulado = $ticketAnulado;
        $this->cantidadAnulada = $cantidadAnulada;
        $this->fechaEmision = $fechaEmision;
        $this->fechaModificacion = $fechaModificacion;
        $this->idEstadoVenta = $idEstadoVenta;
        $this->estado = $estado;
    }

    // Convert an Entity to DTO
    public static function fromEntity(Ventas $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getUuid(),
            $entity->getDetalleZonaServicioHorarioClienteFacturacionId(),
            $entity->getClienteId(),
            $entity->getCantidadFacturada(),
            $entity->isTicketAnulado(),
            $entity->getCantidadAnulada(),
            $entity->getFechaEmision(),
            $entity->getFechaModificacion(),
            $entity->getEstadoVentaId(),
            $entity->isEstado()
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

    public function getIdDetalleZonaServicioHorarioClienteFacturacion(): int
    {
        return $this->idDetalleZonaServicioHorarioClienteFacturacion;
    }

    public function getIdCliente(): int
    {
        return $this->idCliente;
    }

    public function getCantidadFacturada(): int
    {
        return $this->cantidadFacturada;
    }

    public function isTicketAnulado(): bool
    {
        return $this->ticketAnulado;
    }

    public function getCantidadAnulada(): int
    {
        return $this->cantidadAnulada;
    }

    public function getFechaEmision(): \DateTime
    {
        return $this->fechaEmision;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function getIdEstadoVenta(): int
    {
        return $this->idEstadoVenta;
    }

    public function isEstado(): bool
    {
        return $this->estado;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'idDetalleZonaServicioHorarioClienteFacturacion' => $this->idDetalleZonaServicioHorarioClienteFacturacion,
            'cantidadFacturada' => $this->cantidadFacturada,
            'ticketAnulado' => $this->ticketAnulado,
            'cantidadAnulada' => $this->cantidadAnulada,
            'fechaEmision' => $this->fechaEmision->format(\DateTime::ISO8601),
            'fechaModificacion' => $this->fechaModificacion ? $this->fechaModificacion->format(\DateTime::ISO8601) : null,
            'idEstadoVenta' => $this->idEstadoVenta,
            'estado' => $this->estado,

        ];
    }
}
