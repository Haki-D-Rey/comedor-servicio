<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalogo.lista_catalogo_detalle')]
class ListaCatalogoDetalle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_lista_catalogo', type: 'integer')]
    private int $id_lista_catalogo;

    #[ORM\Column(name: 'id_valor', type: 'integer', unique: true)]
    private int $id_valor;

    #[ORM\Column(name: 'codigo_interno', type: 'string', length: 64)]
    private string $codigo_interno;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    /**
     * Many ListaCatalogoDetalle have One ListaCatalogo.
     * @var ListaCatalogo
     */
    #[ORM\ManyToOne(targetEntity: ListaCatalogo::class)]
    #[ORM\JoinColumn(name: 'id_lista_catalogo', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?ListaCatalogo $listaCatalogo = null;

    public function __construct()
    {
        // Constructor logic here
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdListaCatalogo(): int
    {
        return $this->id_lista_catalogo;
    }

    public function setIdListaCatalogo(int $id_lista_catalogo): self
    {
        $this->id_lista_catalogo = $id_lista_catalogo;
        return $this;
    }

    public function getIdValor(): int
    {
        return $this->id_valor;
    }

    public function setIdValor(int $id_valor): self
    {
        $this->id_valor = $id_valor;
        return $this;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
    }

    public function setCodigoInterno(string $codigo_interno): self
    {
        $this->codigo_interno = $codigo_interno;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTime $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function setFechaModificacion(?\DateTime $fecha_modificacion): self
    {
        $this->fecha_modificacion = $fecha_modificacion;
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

    public function getListaCatalogo(): ?ListaCatalogo
    {
        return $this->listaCatalogo;
    }

    public function setListaCatalogo(?ListaCatalogo $listaCatalogo): self
    {
        $this->listaCatalogo = $listaCatalogo;
        return $this;
    }
}
