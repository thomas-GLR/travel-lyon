<?php

namespace App\Entity;

use App\Repository\TransportEnCommunRepository;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransportEnCommunRepository::class)]
class TransportEnCommun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du transport est obligatoire")]
    #[Assert\Length(min: 1, max: 255, minMessage: "Le nom du transport doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom du transport ne peut pas faire plus de {{ limit }} caractères")]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?string $nomTransport = null;

    #[ORM\ManyToOne(inversedBy: 'Transports')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    #[Groups(["getTransports"])]
    private ?TypeTransport $typeTransport = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getTransports"])]
    #[Since("2.0")]
    private ?string $terminusDepart = null;

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

    public function getTerminusDepart(): ?string
    {
        return $this->terminusDepart;
    }

    public function setTerminusDepart(string $terminusDepart): static
    {
        $this->terminusDepart = $terminusDepart;

        return $this;
    }
}
