<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $ref = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La note ne peut pas être vide.")]
    #[Assert\Type(type: "integer", message: "La note doit être un nombre entier.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $note = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le commentaire ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le commentaire doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date de l'avis est obligatoire.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "La date doit être valide.")]
    private ?\DateTimeInterface $date_avis = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, name: 'id_user', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "Une consultation doit être associée à l'avis.")]
    private ?IdUser $user = null;

    #[ORM\ManyToOne(targetEntity: IdUser::class)]
    #[ORM\JoinColumn(nullable: false, name: 'professional_id', referencedColumnName: 'id')]
    private ?IdUser $professional = null;

    public function getProfessional(): ?IdUser
    {
        return $this->professional;
    }

    public function setProfessional(?IdUser $professional): self
    {
        $this->professional = $professional;
        return $this;
    }

    public function getRef(): ?int
    {
        return $this->ref;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDateAvis(): ?\DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDateAvis(?\DateTimeInterface $date_avis): static
    {
        $this->date_avis = $date_avis;
        return $this;
    }

    public function getUser(): ?IdUser
    {
        return $this->user;
    }

    public function setUser(IdUser $user): static
    {
        $this->user = $user;
        return $this;
    }
}