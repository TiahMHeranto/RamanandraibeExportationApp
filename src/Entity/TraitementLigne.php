<?php

namespace App\Entity;

use App\Enum\CategorieLigneTraitement;
use App\Repository\TraitementLigneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TraitementLigneRepository::class)]
class TraitementLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Traitement $traitement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Article $article = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Couleur $couleur = null;

    #[ORM\Column(enumType: CategorieLigneTraitement::class)]
    private CategorieLigneTraitement $categorie = CategorieLigneTraitement::Produit;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 3)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $poids = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombre = null;

    public function getId(): ?int { return $this->id; }
    public function getTraitement(): ?Traitement { return $this->traitement; }
    public function setTraitement(?Traitement $t): static { $this->traitement = $t; return $this; }
    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $a): static { $this->article = $a; return $this; }
    public function getCouleur(): ?Couleur { return $this->couleur; }
    public function setCouleur(?Couleur $c): static { $this->couleur = $c; return $this; }
    public function getCategorie(): CategorieLigneTraitement { return $this->categorie; }
    public function setCategorie(CategorieLigneTraitement $c): static { $this->categorie = $c; return $this; }
    public function getPoids(): ?string { return $this->poids; }
    public function setPoids(string $p): static { $this->poids = $p; return $this; }
    public function getNombre(): ?int { return $this->nombre; }
    public function setNombre(?int $n): static { $this->nombre = $n; return $this; }
}
