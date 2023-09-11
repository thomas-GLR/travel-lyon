<?php

namespace App\Entity;

use App\Repository\TypeTransportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TypeTransportRepository::class)]
class TypeTransport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getTransports"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getTransports"])]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typeTransport', targetEntity: TransportEnCommun::class)]
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
