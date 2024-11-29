<?php

namespace App\Entity\Publico;

use App\Entity\Publico\DetalleZonaServicioHorarioClienteFacturacion;
use App\Entity\ListaCatalogoDetalle;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'ventas', schema: 'public')]
class Ventas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(name: 'uuid', type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: DetalleZonaServicioHorarioClienteFacturacion::class)]
    #[ORM\JoinColumn(name: 'id_detalle_zona_servicio_horario_cliente_facturacion', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DetalleZonaServicioHorarioClienteFacturacion $detalleZonaServicioHorarioClienteFacturacion;

    #[ORM\ManyToOne(targetEntity: Clientes::class)]
    #[ORM\JoinColumn(name: 'id_cliente', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Clientes $cliente;

    #[ORM\Column(name: 'cantidad_facturada', type: 'integer', options: ['default' => 0])]
    private int $cantidadFacturada = 0;

    #[ORM\Column(name: 'ticket_anulado', type: 'boolean', options: ['default' => false])]
    private bool $ticketAnulado = false;

    #[ORM\Column(name: 'cantidad_anulada', type: 'integer', options: ['default' => 0])]
    private int $cantidadAnulada = 0;

    #[ORM\Column(name: 'fecha_emision', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fechaEmision;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaModificacion = null;

    #[ORM\ManyToOne(targetEntity: ListaCatalogoDetalle::class)]
    #[ORM\JoinColumn(name: 'id_estado_venta', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ListaCatalogoDetalle $estadoVenta;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado = true;

    public function __construct()
    {
        // Genera automáticamente un UUID al crear un nuevo registro
        $this->uuid = Uuid::uuid4(); // Genera un nuevo UUID
        $this->fechaEmision = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid->toString();
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getDetalleZonaServicioHorarioClienteFacturacion(): DetalleZonaServicioHorarioClienteFacturacion
    {
        return $this->detalleZonaServicioHorarioClienteFacturacion;
    }

    public function setDetalleZonaServicioHorarioClienteFacturacion(DetalleZonaServicioHorarioClienteFacturacion $detalleZonaServicioHorarioClienteFacturacion): self
    {
        $this->detalleZonaServicioHorarioClienteFacturacion = $detalleZonaServicioHorarioClienteFacturacion;
        return $this;
    }

    public function getCliente(): Clientes
    {
        return $this->cliente;
    }

    public function setCliente(Clientes $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function getCantidadFacturada(): int
    {
        return $this->cantidadFacturada;
    }

    public function setCantidadFacturada(int $cantidadFacturada): self
    {
        $this->cantidadFacturada = $cantidadFacturada;
        return $this;
    }

    public function isTicketAnulado(): bool
    {
        return $this->ticketAnulado;
    }

    public function setTicketAnulado(bool $ticketAnulado): self
    {
        $this->ticketAnulado = $ticketAnulado;
        return $this;
    }

    public function getCantidadAnulada(): int
    {
        return $this->cantidadAnulada;
    }

    public function setCantidadAnulada(int $cantidadAnulada): self
    {
        $this->cantidadAnulada = $cantidadAnulada;
        return $this;
    }

    public function getFechaEmision(): \DateTime
    {
        return $this->fechaEmision;
    }

    public function setFechaEmision(\DateTime $fechaEmision): self
    {
        $this->fechaEmision = $fechaEmision;
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

    public function getEstadoVenta(): ListaCatalogoDetalle
    {
        return $this->estadoVenta;
    }

    public function setEstadoVenta(ListaCatalogoDetalle $estadoVenta): self
    {
        $this->estadoVenta = $estadoVenta;
        return $this;
    }

    public function isEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    // Additional methods for related entity IDs
    public function getDetalleZonaServicioHorarioClienteFacturacionId(): ?int
    {
        return $this->detalleZonaServicioHorarioClienteFacturacion->getId();
    }

    public function getClienteId(): ?int
    {
        return $this->cliente->getId();
    }

    public function getEstadoVentaId(): ?int
    {
        return $this->estadoVenta->getId();
    }
}
