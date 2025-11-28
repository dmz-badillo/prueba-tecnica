<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeImmutable;

/**
 * Class Provider
 *
 * Entidad que representa un proveedor de servicios de mensajería.
 * Contiene la información básica nombre, URL consultada y
 * el estado de la respuesta obtenida (success/error).
 *
 * Esta entidad se usa para almacenar en la base de datos el resultado
 * de las simulaciones realizadas hacia proveedores externos.
 *
 * @author Arely Dominguez
 * @date 25-11-2025
 */

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $status = null; 
    
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updated_at = null;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }
}
