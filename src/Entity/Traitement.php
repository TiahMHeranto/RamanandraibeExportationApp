<?php

namespace App\Entity;

use App\Enum\CategorieLigneTraitement;
use App\Repository\TraitementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TraitementRepository::class)]
class Traitement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateTraitement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Hangar $hangar = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Personnel $trieuse = null;

    #[ORM\ManyToOne]
    private ?Personnel $controleuse = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne]
    private ?Contrat $contrat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Article $articleSource = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Couleur $couleurSource = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Magasin $magasin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 3)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $poidsSortie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, TraitementLigne> */
    #[ORM\OneToMany(targetEntity: TraitementLigne::class, mappedBy: 'traitement', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    /** @var Collection<int, MouvementStock> */
    #[ORM\OneToMany(targetEntity: MouvementStock::class, mappedBy: 'traitement')]
    private Collection $mouvements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->dateTraitement = new \DateTimeImmutable('today');
        $this->lignes = new ArrayCollection();
        $this->mouvements = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): ?string { return $this->reference; }
    public function setReference(string $r): static { $this->reference = strtoupper(trim($r)); return $this; }
    public function getDateTraitement(): ?\DateTimeImmutable { return $this->dateTraitement; }
    public function setDateTraitement(\DateTimeImmutable $d): static { $this->dateTraitement = $d; return $this; }
    public function getHangar(): ?Hangar { return $this->hangar; }
    public function setHangar(?Hangar $h): static { $this->hangar = $h; return $this; }
    public function getTrieuse(): ?Personnel { return $this->trieuse; }
    public function setTrieuse(?Personnel $p): static { $this->trieuse = $p; return $this; }
    public function getControleuse(): ?Personnel { return $this->controleuse; }
    public function setControleuse(?Personnel $p): static { $this->controleuse = $p; return $this; }
    public function getFournisseur(): ?Fournisseur { return $this->fournisseur; }
    public function setFournisseur(?Fournisseur $f): static { $this->fournisseur = $f; return $this; }
    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $c): static { $this->contrat = $c; return $this; }
    public function getArticleSource(): ?Article { return $this->articleSource; }
    public function setArticleSource(?Article $a): static { $this->articleSource = $a; return $this; }
    public function getCouleurSource(): ?Couleur { return $this->couleurSource; }
    public function setCouleurSource(?Couleur $c): static { $this->couleurSource = $c; return $this; }
    public function getMagasin(): ?Magasin { return $this->magasin; }
    public function setMagasin(?Magasin $m): static { $this->magasin = $m; return $this; }
    public function getPoidsSortie(): ?string { return $this->poidsSortie; }
    public function setPoidsSortie(string $p): static { $this->poidsSortie = $p; return $this; }
    public function getObservations(): ?string { return $this->observations; }
    public function setObservations(?string $o): static { $this->observations = $o; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, TraitementLigne> */
    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(TraitementLigne $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setTraitement($this);
        }
        return $this;
    }

    public function removeLigne(TraitementLigne $ligne): static
    {
        if ($this->lignes->removeElement($ligne) && $ligne->getTraitement() === $this) {
            $ligne->setTraitement(null);
        }
        return $this;
    }

    /** @return Collection<int, MouvementStock> */
    public function getMouvements(): Collection { return $this->mouvements; }

    public function getPoidsEntrees(): string
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += (float) $ligne->getPoids();
        }
        return number_format($total, 3, '.', '');
    }

    public function getEcartPoids(): string
    {
        return number_format((float) $this->poidsSortie - (float) $this->getPoidsEntrees(), 3, '.', '');
    }

    public function __toString(): string
    {
        return (string) $this->reference;
    }
}
