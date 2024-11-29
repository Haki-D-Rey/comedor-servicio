<?php

namespace App\DTO\Publico;

use JsonSerializable;

class ClientesCreditoPeriodicoDTO implements JsonSerializable
{
    private $id;
    private $id_detalle_zona_servicio_horario_cliente_facturacion;
    private $periodo_inicial;
    private $periodo_final;
    private $cantidad_credito_limite;
    private $cantidad_credito_usado;
    private $cantidad_credito_disponible;
    private $fecha_creacion;
    private $fecha_modificacion = null;
    private $estado;

    public function __construct(
        ?int $id,
        int $id_detalle_zona_servicio_horario_cliente_facturacion,
        \DateTime $periodo_inicial,
        \DateTime $periodo_final,
        int $cantidad_credito_limite,
        int $cantidad_credito_usado,
        int $cantidad_credito_disponible,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->id_detalle_zona_servicio_horario_cliente_facturacion = $id_detalle_zona_servicio_horario_cliente_facturacion;
        $this->periodo_inicial = $periodo_inicial;
        $this->periodo_final = $periodo_final;
        $this->cantidad_credito_limite = $cantidad_credito_limite;
        $this->cantidad_credito_usado = $cantidad_credito_usado;
        $this->cantidad_credito_disponible = $cantidad_credito_disponible;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetalleZonaServicioHorarioClienteFacturacion(): int
    {
        return $this->id_detalle_zona_servicio_horario_cliente_facturacion;
    }

    public function getPeriodoInicial(): \DateTime
    {
        return $this->periodo_inicial;
    }

    public function getPeriodoFinal(): \DateTime
    {
        return $this->periodo_final;
    }

    public function getCantidadCreditoLimite(): int
    {
        return $this->cantidad_credito_limite;
    }

    public function getCantidadCreditoUsado(): int
    {
        return $this->cantidad_credito_usado;
    }

    public function getCantidadCreditoDisponible(): int
    {
        return $this->cantidad_credito_disponible;
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
            'id_detalle_zona_servicio_horario_cliente_facturacion' => $this->id_detalle_zona_servicio_horario_cliente_facturacion,
            'periodo_inicial' => $this->periodo_inicial->format(\DateTime::ISO8601),
            'periodo_final' => $this->periodo_final->format(\DateTime::ISO8601),
            'cantidad_credito_limite' => $this->cantidad_credito_limite,
            'cantidad_credito_usado' => $this->cantidad_credito_usado,
            'cantidad_credito_disponible' => $this->cantidad_credito_disponible,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado
        ];
    }
}
