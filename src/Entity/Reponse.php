<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La reponse ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "La reponse doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La reponse ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $reponse = "";

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_reponse = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, name: 'id_avis', referencedColumnName: 'ref')]
    private ?Avis $avis = null;

    #[ORM\ManyToOne(targetEntity: IdUser::class)]
    #[ORM\JoinColumn(nullable: false, name: 'professional_id', referencedColumnName: 'id')]
    private ?IdUser $professional = null;

    #[ORM\ManyToOne(targetEntity: IdUser::class)]
    #[ORM\JoinColumn(nullable: true, name: 'madeBy_id', referencedColumnName: 'id')]
    private ?IdUser $madeBy = null;

    public function getProfessional(): ?IdUser
    {
        return $this->professional;
    }

    public function getMadeBy(): ?IdUser
    {
        return $this->madeBy;
    }

    public function setMadeBy(?IdUser $madeBy): void
    {
        $this->madeBy = $madeBy;
    }

    public function setProfessional(?IdUser $professional): self
    {
        $this->professional = $professional;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse ?? "";

        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDateReponse(\DateTimeInterface $date_reponse): static
    {
        $this->date_reponse = $date_reponse;

        return $this;
    }

    public function getAvis(): ?Avis
    {
        return $this->avis;
    }

    public function setAvis(Avis $avis): static
    {
        $this->avis = $avis;

        return $this;
    }
}
