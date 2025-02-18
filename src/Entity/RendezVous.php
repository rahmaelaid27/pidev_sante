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

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "The patient ID cannot be empty.")]
    #[Assert\Length(max: 255, maxMessage: "The patient ID must not exceed 255 characters.")]
    private ?string $id_patient = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "The date cannot be empty.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "Invalid date format.")]
    private ?\DateTimeInterface $date_rendez_vous = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Payment status is required.")]
    #[Assert\Choice(choices: ["paid", "pending", "failed"], message: "Choose a valid payment status.")]
    private ?string $statu_paiement = null;

    #[ORM\OneToOne(mappedBy: 'id_rendez_vous', cascade: ['persist', 'remove'])]
    private ?Facture $Facture = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPatient(): ?string
    {
        return $this->id_patient;
    }

    public function setIdPatient(?string $id_patient): static
    {
        $this->id_patient = $id_patient;
        return $this;
    }

    public function getDateRendezVous(): ?\DateTimeInterface
    {
        return $this->date_rendez_vous;
    }

    public function setDateRendezVous(?\DateTimeInterface $date_rendez_vous): static
    {
        $this->date_rendez_vous = $date_rendez_vous;
        return $this;
    }

    public function getStatuPaiement(): ?string
    {
        return $this->statu_paiement;
    }

    public function setStatuPaiement(?string $statu_paiement): static
    {
        $this->statu_paiement = $statu_paiement;
        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->Facture;
    }

    public function setFacture(?Facture $Facture): static
    {
        // unset the owning side of the relation if necessary
        if ($Facture === null && $this->Facture !== null) {
            $this->Facture->setIdRendezVous(null);
        }

        // set the owning side of the relation if necessary
        if ($Facture !== null && $Facture->getIdRendezVous() !== $this) {
            $Facture->setIdRendezVous($this);
        }

        $this->Facture = $Facture;

        return $this;
    }

    
}
?>
