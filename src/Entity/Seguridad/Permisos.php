<?php
// src/Entity/Permiso.php
namespace App\Entity\Seguridad;

use App\Entity\Seguridad\TipoUsuarioPermisos;
use App\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'seguridad.permisos')]
class Permisos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 128)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 128)]
    private string $accion;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $estado = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $codigo_interno;


    // // Relación inversa OneToMany con la tabla intermedia
    #[ORM\OneToMany(mappedBy: 'permiso', targetEntity: TipoUsuarioPermisos::class)]
    private Collection $tipoUsuarioPermisos;

    // #[ORM\ManyToMany(targetEntity: Usuario::class, mappedBy: 'permisos')]
    // private Usuario $usuarios;

    // public function __construct()
    // {
    //     $this->tipoUsuarioPermisos = new ArrayCollection();
    // }


    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getAccion(): string
    {
        return $this->accion;
    }

    public function setAccion(string $accion): self
    {
        $this->accion = $accion;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
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
}
