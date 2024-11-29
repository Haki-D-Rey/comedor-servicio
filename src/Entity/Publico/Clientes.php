<?php
namespace App\Entity\Publico;

use App\Entity\ListaCatalogoDetalle;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cliente', schema: 'public')]
class Clientes
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nombres', type: 'string', length: 128, nullable: false)]
    private string $nombres;

    #[ORM\Column(name: 'apellidos', type: 'string', length: 128, nullable: false)]
    private string $apellidos;

    // Relación con Departamento sin persistencia automática
    #[ORM\ManyToOne(targetEntity: ListaCatalogoDetalle::class )]
    #[ORM\JoinColumn(name: 'id_departamento', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ListaCatalogoDetalle $departamento;

    // Relación con Cargo sin persistencia automática
    #[ORM\ManyToOne(targetEntity: ListaCatalogoDetalle::class)]
    #[ORM\JoinColumn(name: 'id_cargo', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ListaCatalogoDetalle $cargo;

    #[ORM\Column(name: 'correo', type: 'string', length: 256, nullable: false, unique: true)]
    private string $correo;

    #[ORM\Column(name: 'clie_docnum', type: 'string', length: 64, nullable: false, unique: true)]
    private string $clieDocnum;

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

    public function getNombres(): string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;
        return $this;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    public function getDepartamento(): ListaCatalogoDetalle
    {
        return $this->departamento;
    }

    public function setDepartamento(ListaCatalogoDetalle $departamento): self
    {
        $this->departamento = $departamento;
        return $this;
    }

    public function getCargo(): ListaCatalogoDetalle
    {
        return $this->cargo;
    }

    public function setCargo(ListaCatalogoDetalle $cargo): self
    {
        $this->cargo = $cargo;
        return $this;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;
        return $this;
    }

    public function getClieDocnum(): string
    {
        return $this->clieDocnum;
    }

    public function setClieDocnum(string $clieDocnum): self
    {
        $this->clieDocnum = $clieDocnum;
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
