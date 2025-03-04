<?php

namespace App\Entity;

use App\Repository\BlocsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlocsRepository::class)]
class Blocs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    /**
     * @var Collection<int, Specialites>
     */
    #[ORM\OneToMany(targetEntity: Specialites::class, mappedBy: 'id_bloc')]
    private Collection $statut_spec;

    public function __construct()
    {
        $this->statut_spec = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Specialites>
     */
    public function getStatutSpec(): Collection
    {
        return $this->statut_spec;
    }

    public function addStatutSpec(Specialites $statutSpec): static
    {
        if (!$this->statut_spec->contains($statutSpec)) {
            $this->statut_spec->add($statutSpec);
            $statutSpec->setIdBloc($this);
        }

        return $this;
    }

    public function removeStatutSpec(Specialites $statutSpec): static
    {
        if ($this->statut_spec->removeElement($statutSpec)) {
            // set the owning side to null (unless already changed)
            if ($statutSpec->getIdBloc() === $this) {
                $statutSpec->setIdBloc(null);
            }
        }

        return $this;
    }
}
