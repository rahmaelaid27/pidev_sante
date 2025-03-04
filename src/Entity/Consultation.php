<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\IdUser;


#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
//    #[Assert\NotBlank(message: 'This field cannot be blank.')]
    private ?\DateTimeInterface $date_consultation = null;

    #[Assert\NotNull(message: "Le patient doit être sélectionné.")]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'id_user', referencedColumnName: 'id')]
    private ?IdUser $user = null;

    #[Assert\NotNull(message: "Le professionnel de santé doit être sélectionné.")]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'id_professionnel', referencedColumnName: 'id')]
    private ?IdUser $professionnel = null;

    #[ORM\OneToOne(targetEntity: RendezVous::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?RendezVous $rendezVous = null;

    #[ORM\OneToMany(targetEntity: Prescription::class, mappedBy: "consultation", cascade: ['remove'], orphanRemoval: true)]
    private Collection $prescriptions;

    #[ORM\Column(type: Types::STRING, nullable: true)]
//    #[Assert\NotNull(message: "Ce champ ne peut pas être null.")]
//    #[Assert\NotBlank(message: "Ce champ ne peut pas être vide.")]
//    #[Assert\Length(min: 3, minMessage: "Le motif doit contenir au moins {{ limit }} caractères.")]
    private ?string $reason = null;

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }


    public function __construct()
    {
        $this->prescriptions = new ArrayCollection();
    }

    public function getRendezVous(): ?RendezVous
    {
        return $this->rendezVous;
    }

    public function setRendezVous(?RendezVous $rendezVous): void
    {
        $this->rendezVous = $rendezVous;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateConsultation(): ?\DateTimeInterface
    {
        return $this->date_consultation;
    }

    public function setDateConsultation(?\DateTimeInterface $date_consultation): static
    {
        $this->date_consultation = $date_consultation;
        return $this;
    }

    public function getUser(): ?IdUser
    {
        return $this->user;
    }

    public function setUser(?IdUser $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getProfessionnel(): ?IdUser
    {
        return $this->professionnel;
    }

    public function setProfessionnel(?IdUser $professionnel): static
    {
        $this->professionnel = $professionnel;
        return $this;
    }

    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }

    public function addPrescription(Prescription $prescription): static
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions->add($prescription);
            $prescription->setConsultation($this);
        }
        return $this;
    }

    public function removePrescription(Prescription $prescription): static
    {
        if ($this->prescriptions->removeElement($prescription)) {
            if ($prescription->getConsultation() === $this) {
                $prescription->setConsultation(null);
            }
        }
        return $this;
    }




}
