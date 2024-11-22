<?php

namespace App\DTO\Publico;

use JsonSerializable;

class DetalleClienteIdentificacionFacturacionDTO implements JsonSerializable
{
    private $id;
    private $id_cliente;
    private $id_identificacion_facturacion;
    private $json_identificacion;
    private $fecha_creacion;
    private $fecha_modificacion = null;
    private $estado;

    public function __construct(
        ?int $id,
        int $id_cliente,
        int $id_identificacion_facturacion,
        ?array $json_identificacion,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->id_cliente = $id_cliente;
        $this->id_identificacion_facturacion = $id_identificacion_facturacion;
        $this->json_identificacion = $json_identificacion;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    public function getIdIdentificacionFacturacion(): int
    {
        return $this->id_identificacion_facturacion;
    }

    public function getJsonIdentificacion(): ?array
    {
        return $this->json_identificacion;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    // Implementación de JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'id_cliente' => $this->id_cliente,
            'id_identificacion_facturacion' => $this->id_identificacion_facturacion,
            'json_identificacion' => $this->json_identificacion,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado
        ];
    }
}
