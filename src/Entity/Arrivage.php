<?php

namespace App\Entity;

use App\Repository\ArrivageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArrivageRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_ARRIVAGE_NUMERO', fields: ['numero'])]
#[UniqueEntity(fields: ['numero'], message: 'Ce N° d\'arrivage existe déjà.')]
class Arrivage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private ?string $numero = null;

    #[ORM\ManyToOne(inversedBy: 'arrivages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    private ?string $origine = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 3)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $poids = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateArrivage = null;

    #[ORM\ManyToOne]
    private ?Article $article = null;

    #[ORM\ManyToOne]
    private ?Couleur $couleur = null;

    #[ORM\ManyToOne]
    private ?Magasin $magasin = null;

    #[ORM\ManyToOne]
    private ?Contrat $contrat = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->dateArrivage = new \DateTimeImmutable('today');
    }

    public function getId(): ?int { return $this->id; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = strtoupper(trim($numero)); return $this; }
    public function getFournisseur(): ?Fournisseur { return $this->fournisseur; }
    public function setFournisseur(?Fournisseur $fournisseur): static { $this->fournisseur = $fournisseur; return $this; }
    public function getOrigine(): ?string { return $this->origine; }
    public function setOrigine(string $origine): static { $this->origine = trim($origine); return $this; }
    public function getPoids(): ?string { return $this->poids; }
    public function setPoids(string $poids): static { $this->poids = $poids; return $this; }
    public function getDateArrivage(): ?\DateTimeImmutable { return $this->dateArrivage; }
    public function setDateArrivage(\DateTimeImmutable $dateArrivage): static { $this->dateArrivage = $dateArrivage; return $this; }
    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $article): static { $this->article = $article; return $this; }
    public function getCouleur(): ?Couleur { return $this->couleur; }
    public function setCouleur(?Couleur $couleur): static { $this->couleur = $couleur; return $this; }
    public function getMagasin(): ?Magasin { return $this->magasin; }
    public function setMagasin(?Magasin $magasin): static { $this->magasin = $magasin; return $this; }
    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $contrat): static { $this->contrat = $contrat; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
    public function __toString(): string { return (string) $this->numero; }
}
