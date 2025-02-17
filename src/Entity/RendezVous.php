<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date du rendez-vous ne peut pas être vide.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "Veuillez entrer une date valide.")]
    private ?\DateTimeInterface $date_rdv = null;

    #[ORM\Column(length: 255)]
    private ?string $status_rdv = "en attente";

    public function __construct()
    {
        $this->status_rdv = "en attente";
    }
    #[ORM\OneToOne(targetEntity: Consultation::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Consultation $consultation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, name: 'id_user', referencedColumnName: 'ref')]
    #[Assert\NotNull(message: "Une consultation doit être associée à l'avis.")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, name: 'professional_id', referencedColumnName: 'ref')]
    private ?User $professional = null;

    public function getProfessional(): ?User
    {
        return $this->professional;
    }

    public function setProfessional(?User $professional): self
    {
        $this->professional = $professional;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->date_rdv;
    }

    public function setDateRdv(\DateTimeInterface $date_rdv): static
    {
        $this->date_rdv = $date_rdv;
        return $this;
    }

    public function acceptRdv(): void
    {
        $this->status_rdv = 'accepté';
    }

    public function rejectRdv(): void
    {
        $this->status_rdv = 'refusé';
    }

    public function getStatusRdv(): ?string
    {
        return $this->status_rdv;
    }

    public function setStatusRdv(string $status_rdv): static
    {
        $this->status_rdv = $status_rdv;
        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;
        return $this;
    }
}
