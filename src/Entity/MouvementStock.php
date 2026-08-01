<?php

namespace App\Entity;

use App\Enum\SensMouvement;
use App\Enum\TypeOperationStock;
use App\Repository\MouvementStockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MouvementStockRepository::class)]
#[ORM\Index(columns: ['date_mouvement'], name: 'IDX_MOUVEMENT_DATE')]
class MouvementStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateMouvement = null;

    #[ORM\Column(enumType: SensMouvement::class)]
    private SensMouvement $sens = SensMouvement::Entree;

    #[ORM\Column(enumType: TypeOperationStock::class)]
    private TypeOperationStock $typeOperation = TypeOperationStock::Autre;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Article $article = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Couleur $couleur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Magasin $magasin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 3)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $poids = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $reference = null;

    #[ORM\ManyToOne]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne]
    private ?Contrat $contrat = null;

    #[ORM\ManyToOne]
    private ?Hangar $hangar = null;

    #[ORM\ManyToOne]
    private ?Arrivage $arrivage = null;

    #[ORM\ManyToOne(inversedBy: 'mouvements')]
    private ?Traitement $traitement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->dateMouvement = new \DateTimeImmutable('today');
    }

    public function getId(): ?int { return $this->id; }
    public function getDateMouvement(): ?\DateTimeImmutable { return $this->dateMouvement; }
    public function setDateMouvement(\DateTimeImmutable $d): static { $this->dateMouvement = $d; return $this; }
    public function getSens(): SensMouvement { return $this->sens; }
    public function setSens(SensMouvement $s): static { $this->sens = $s; return $this; }
    public function getTypeOperation(): TypeOperationStock { return $this->typeOperation; }
    public function setTypeOperation(TypeOperationStock $t): static { $this->typeOperation = $t; return $this; }
    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $a): static { $this->article = $a; return $this; }
    public function getCouleur(): ?Couleur { return $this->couleur; }
    public function setCouleur(?Couleur $c): static { $this->couleur = $c; return $this; }
    public function getMagasin(): ?Magasin { return $this->magasin; }
    public function setMagasin(?Magasin $m): static { $this->magasin = $m; return $this; }
    public function getPoids(): ?string { return $this->poids; }
    public function setPoids(string $p): static { $this->poids = $p; return $this; }
    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $r): static { $this->reference = $r !== null ? strtoupper(trim($r)) : null; return $this; }
    public function getFournisseur(): ?Fournisseur { return $this->fournisseur; }
    public function setFournisseur(?Fournisseur $f): static { $this->fournisseur = $f; return $this; }
    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $c): static { $this->contrat = $c; return $this; }
    public function getHangar(): ?Hangar { return $this->hangar; }
    public function setHangar(?Hangar $h): static { $this->hangar = $h; return $this; }
    public function getArrivage(): ?Arrivage { return $this->arrivage; }
    public function setArrivage(?Arrivage $a): static { $this->arrivage = $a; return $this; }
    public function getTraitement(): ?Traitement { return $this->traitement; }
    public function setTraitement(?Traitement $t): static { $this->traitement = $t; return $this; }
    public function getObservations(): ?string { return $this->observations; }
    public function setObservations(?string $o): static { $this->observations = $o; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function signedPoids(): float
    {
        $w = (float) $this->poids;
        return $this->sens === SensMouvement::Entree ? $w : -$w;
    }
}
