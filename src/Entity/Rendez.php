<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Rendez
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Patient (set from logged-in user)
    #[ORM\ManyToOne(targetEntity: \App\Entity\IdUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Patient is required.")]
    private ?\App\Entity\IdUser $user = null;

    // Professional selected via form
    #[ORM\ManyToOne(targetEntity: \App\Entity\IdUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Professional is required.")]
    private ?\App\Entity\IdUser $professional = null;

    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "Date is required.")]
    private ?\DateTimeInterface $dateRendez = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Payment status is required.")]
    private ?string $statuPaiement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?\App\Entity\IdUser
    {
        return $this->user;
    }
    public function setUser(?\App\Entity\IdUser $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getProfessional(): ?\App\Entity\IdUser
    {
        return $this->professional;
    }
    public function setProfessional(?\App\Entity\IdUser $professional): self
    {
        $this->professional = $professional;
        return $this;
    }

    public function getDateRendez(): ?\DateTimeInterface
    {
        return $this->dateRendez;
    }
    public function setDateRendez(?\DateTimeInterface $dateRendez): self
    {
        $this->dateRendez = $dateRendez;
        return $this;
    }

    public function getStatuPaiement(): ?string
    {
        return $this->statuPaiement;
    }
    public function setStatuPaiement(?string $statuPaiement): self
    {
        $this->statuPaiement = $statuPaiement;
        return $this;
    }
}
