<?php

namespace App\Entity;

use App\Entity\Seguridad\TipoUsuarioPermisos;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity]
#[ORM\Table(name: 'seguridad.usuarios')]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nombre_usuario', type: 'string', length: 64, unique: true)]
    private string $nombreUsuario;

    #[ORM\Column(name: 'contrasenia', type: 'string', length: 512)]
    private string $contrasenia;

    #[ORM\Column(name: 'nombres', type: 'string', length: 64)]
    private string $nombres;

    #[ORM\Column(name: 'apellidos', type: 'string', length: 64)]
    private string $apellidos;

    #[ORM\Column(name: 'correo', type: 'string', length: 64, unique: true)]
    private string $correo;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(name: 'isadmin', type: 'boolean', options: ['default' => false])]
    private bool $isAdmin;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    #[ORM\Column(name: 'id_tipo_usuario_permiso', type: 'integer')]
    private int $idTipoUsuarioPermiso = 1;

    #[ORM\OneToMany(targetEntity: TipoUsuarioPermisos::class, mappedBy: 'usuario')]
    #[ORM\JoinColumn(name: "usuario_id", referencedColumnName: "id", nullable: false)]
    private PersistentCollection $tipoUsuarioPermiso;

    public function __construct() {
        $this->tipoUsuarioPermiso = new ArrayCollection();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): self
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }

    public function getContrasenia(): ?string
    {
        return $this->contrasenia;
    }

    public function setContrasenia(string $contrasenia): self
    {
        $this->contrasenia = $contrasenia;
        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;
        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTimeInterface
    {
        return $this->fecha_modificacion;
    }

    public function setFechaModificacion(?\DateTimeInterface $fecha_modificacion): self
    {
        $this->fecha_modificacion = $fecha_modificacion;
        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    public function getTipoUsuarioPermisos(): PersistentCollection
    {
        return $this->tipoUsuarioPermiso;
    }

    public function getIdTipoUsuario(): int
    {
        return $this->idTipoUsuarioPermiso;
    }

    public function setIdTipoUsuario(int $id): self
    {
        $this->idTipoUsuarioPermiso = $id;
        return $this;
    }
}
