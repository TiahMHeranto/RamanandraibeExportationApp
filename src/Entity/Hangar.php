<?php

namespace App\Entity;

use App\Repository\HangarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HangarRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_HANGAR_NUMERO', fields: ['numero'])]
#[ORM\UniqueConstraint(name: 'UNIQ_HANGAR_CODE', fields: ['code'])]
#[UniqueEntity(fields: ['numero'], message: 'Ce numéro de hangar existe déjà.')]
#[UniqueEntity(fields: ['code'], message: 'Ce code hangar existe déjà.')]
class Hangar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private ?string $numero = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $code = null;

    #[ORM\ManyToOne]
    private ?Magasin $magasin = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = strtoupper(trim($numero)); return $this; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = strtoupper(trim($code)); return $this; }
    public function getMagasin(): ?Magasin { return $this->magasin; }
    public function setMagasin(?Magasin $magasin): static { $this->magasin = $magasin; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
    public function __toString(): string { return sprintf('%s — %s', $this->numero, $this->code); }
}
