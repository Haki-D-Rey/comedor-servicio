<?php

namespace App\Entity\Publico;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publico\DetalleClienteIdentificacionFacturacion;
use App\Entity\DetalleZonaServicioHorario;

#[ORM\Entity]
#[ORM\Table(name: 'detalle_zona_servicio_horario_cliente_facturacion', schema: 'public')]
class DetalleZonaServicioHorarioClienteFacturacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_detalle_cliente_identificacion_facturacion', type: 'integer', nullable: false)]
    private int $idDetalleClienteIdentificacionFacturacion;

    #[ORM\Column(name: 'id_detalle_zona_servicio_horario', type: 'integer', nullable: false)]
    private int $idDetalleZonaServicioHorario;

    // Relación con DetalleClienteIdentificacionFacturacion sin persistencia automática
    #[ORM\ManyToOne(targetEntity: DetalleClienteIdentificacionFacturacion::class, inversedBy: 'detalleZonaServicioHorarioClienteFacturacion')]
    #[ORM\JoinColumn(name: 'id_detalle_cliente_identificacion_facturacion', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DetalleClienteIdentificacionFacturacion $detalleClienteIdentificacionFacturacion;

    // Relación con DetalleZonaServicioHorario sin persistencia automática
    #[ORM\ManyToOne(targetEntity: DetalleZonaServicioHorario::class)]
    #[ORM\JoinColumn(name: 'id_detalle_zona_servicio_horario', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DetalleZonaServicioHorario $detalleZonaServicioHorario;

    #[ORM\Column(name: 'codigo_interno', type: 'string', length: 64, unique: true)]
    private string $codigoInterno;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fechaCreacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaModificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDetalleClienteIdentificacionFacturacion(): int
    {
        return $this->idDetalleClienteIdentificacionFacturacion;
    }

    public function setIdDetalleClienteIdentificacionFacturacion(int $idDetalleClienteIdentificacionFacturacion): self
    {
        $this->idDetalleClienteIdentificacionFacturacion = $idDetalleClienteIdentificacionFacturacion;
        return $this;
    }

    public function getIdDetalleZonaServicioHorario(): int
    {
        return $this->idDetalleZonaServicioHorario;
    }

    public function setIdDetalleZonaServicioHorario(int $idDetalleZonaServicioHorario): self
    {
        $this->idDetalleZonaServicioHorario = $idDetalleZonaServicioHorario;
        return $this;
    }

    //Estas propiedades ahora no se gestionan con persistencia automática
    public function getDetalleClienteIdentificacionFacturacion(): DetalleClienteIdentificacionFacturacion
    {
        return $this->detalleClienteIdentificacionFacturacion;
    }

    public function setDetalleClienteIdentificacionFacturacion(DetalleClienteIdentificacionFacturacion $detalleClienteIdentificacionFacturacion): self
    {
        $this->detalleClienteIdentificacionFacturacion = $detalleClienteIdentificacionFacturacion;
        return $this;
    }

    public function getDetalleZonaServicioHorario(): DetalleZonaServicioHorario
    {
        return $this->detalleZonaServicioHorario;
    }

    public function setDetalleZonaServicioHorario(DetalleZonaServicioHorario $detalleZonaServicioHorario): self
    {
        $this->detalleZonaServicioHorario = $detalleZonaServicioHorario;
        return $this;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigoInterno;
    }

    public function setCodigoInterno(string $codigoInterno): self
    {
        $this->codigoInterno = $codigoInterno;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTime $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function setFechaModificacion(?\DateTime $fechaModificacion): self
    {
        $this->fechaModificacion = $fechaModificacion;
        return $this;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;
        return $this;
    }
}
