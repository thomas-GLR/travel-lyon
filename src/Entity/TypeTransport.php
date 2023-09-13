<?php

namespace App\Entity;

use App\Repository\TypeTransportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeTransportRepository::class)]
class TypeTransport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le libellé du type de transport est obligatoire")]
    #[Assert\Length(min: 1, max: 255, minMessage: "Le libellé du type de transport doit faire au moins {{ limit }} caractères",
        maxMessage: "Le libellé du type de transport ne peut pas faire plus de {{ limit }} caractères")]
    #[Groups(["getTransports", "getTypeTransports"])]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typeTransport', targetEntity: TransportEnCommun::class)]
    #[Groups(["getTypeTransports"])]
    private Collection $Transports;

    public function __construct()
    {
        $this->Transports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, TransportEnCommun>
     */
    public function getTransports(): Collection
    {
        return $this->Transports;
    }

    public function addTransport(TransportEnCommun $transport): static
    {
        if (!$this->Transports->contains($transport)) {
            $this->Transports->add($transport);
            $transport->setTypeOfTransport($this);
        }

        return $this;
    }

    public function removeTransport(TransportEnCommun $transport): static
    {
        if ($this->Transports->removeElement($transport)) {
            // set the owning side to null (unless already changed)
            if ($transport->getTypeOfTransport() === $this) {
                $transport->setTypeOfTransport(null);
            }
        }

        return $this;
    }
}
