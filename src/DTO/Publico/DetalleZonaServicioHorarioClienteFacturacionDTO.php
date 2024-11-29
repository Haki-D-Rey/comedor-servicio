<?php

namespace App\DTO\Publico;

use JsonSerializable;

class DetalleZonaServicioHorarioClienteFacturacionDTO implements JsonSerializable
{
    private $id;
    private $id_detalle_cliente_identificacion_facturacion;
    private $id_detalle_zona_servicio_horario;
    private $codigo_interno;
    private $fecha_creacion;
    private $fecha_modificacion = null;
    private $estado;

    public function __construct(
        ?int $id,
        int $id_detalle_cliente_identificacion_facturacion,
        int $id_detalle_zona_servicio_horario,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->id_detalle_cliente_identificacion_facturacion = $id_detalle_cliente_identificacion_facturacion;
        $this->id_detalle_zona_servicio_horario = $id_detalle_zona_servicio_horario;
        $this->codigo_interno = $codigo_interno;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetalleClienteIdentificacionFacturacion(): int
    {
        return $this->id_detalle_cliente_identificacion_facturacion;
    }

    public function getIdDetalleZonaServicioHorario(): int
    {
        return $this->id_detalle_zona_servicio_horario;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
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
            'id_detalle_cliente_identificacion_facturacion' => $this->id_detalle_cliente_identificacion_facturacion,
            'id_detalle_zona_servicio_horario' => $this->id_detalle_zona_servicio_horario,
            'codigo_interno' => $this->codigo_interno,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado
        ];
    }
}
