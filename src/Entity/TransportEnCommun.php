<?php

namespace App\Entity;

use App\Repository\TransportEnCommunRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransportEnCommunRepository::class)]
class TransportEnCommun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?string $nomTransport = null;

    #[ORM\ManyToOne(inversedBy: 'Transports')]
    #[Groups(["getTransports"])]
    private ?TypeTransport $typeTransport = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTransport(): ?string
    {
        return $this->nomTransport;
    }

    public function setNomTransport(string $nomTransport): static
    {
        $this->nomTransport = $nomTransport;

        return $this;
    }

    /*
    public function getTypeTransport(): ?int
    {
        return $this->typeTransport;
    }

    public function setTypeTransport(int $typeTransport): static
    {
        $this->typeTransport = $typeTransport;

        return $this;
    }
    */

    public function getTypeTransport(): ?TypeTransport
    {
        return $this->typeTransport;
    }

    public function setTypeTransport(?TypeTransport $typeTransport): static
    {
        $this->typeTransport = $typeTransport;

        return $this;
    }
}
