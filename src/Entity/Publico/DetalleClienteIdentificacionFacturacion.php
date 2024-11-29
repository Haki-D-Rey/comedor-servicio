<?php

namespace App\Entity\Publico;

use App\Entity\Publico\Clientes;
use App\Entity\ListaCatalogoDetalle;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'detalle_cliente_identificacion_facturacion', schema: 'public')]
class DetalleClienteIdentificacionFacturacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_cliente', type: 'integer', nullable: false)]
    private int $id_cliente;

    #[ORM\Column(name: 'id_identificacion_facturacion', type: 'integer', nullable: false)]
    private int $id_identificacion_facturacion;

    #[ORM\ManyToOne(targetEntity: Clientes::class, cascade: ['refresh'])]
    #[ORM\JoinColumn(name: 'id_cliente', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Clientes $cliente;

    #[ORM\ManyToOne(targetEntity: ListaCatalogoDetalle::class, cascade: ['refresh'])]
    #[ORM\JoinColumn(name: 'id_identificacion_facturacion', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ListaCatalogoDetalle $identificacionFacturacion;

    #[ORM\Column(name: 'json_identificacion', type: 'jsonb', nullable: true)]
    private ?array $jsonIdentificacion = null;

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

    // Getter y Setter para id_cliente
    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    public function setIdCliente(int $id_cliente): self
    {
        $this->id_cliente = $id_cliente;
        return $this; // Para permitir el encadenamiento de métodos
    }

    // Getter y Setter para id_identificacion_facturacion
    public function getIdIdentificacionFacturacion(): int
    {
        return $this->id_identificacion_facturacion;
    }

    public function setIdIdentificacionFacturacion(int $id_identificacion_facturacion): self
    {
        $this->id_identificacion_facturacion = $id_identificacion_facturacion;
        return $this; // Para permitir el encadenamiento de métodos
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

    public function getIdentificacionFacturacion(): ListaCatalogoDetalle
    {
        return $this->identificacionFacturacion;
    }

    public function setIdentificacionFacturacion(ListaCatalogoDetalle $identificacionFacturacion): self
    {
        $this->identificacionFacturacion = $identificacionFacturacion;
        return $this;
    }

    public function getJsonIdentificacion(): ?array
    {
        return $this->jsonIdentificacion;
    }

    public function setJsonIdentificacion(?array $jsonIdentificacion): self
    {
        $this->jsonIdentificacion = $jsonIdentificacion;
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
