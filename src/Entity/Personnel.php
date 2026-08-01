<?php

namespace App\Entity;

use App\Enum\RolePersonnel;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PERSONNEL_NUMERO', fields: ['numeroPersonnel'])]
#[UniqueEntity(fields: ['numeroPersonnel'], message: 'Ce numéro de personnel existe déjà.')]
class Personnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private ?string $numeroPersonnel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    #[ORM\Column(enumType: RolePersonnel::class)]
    private RolePersonnel $role = RolePersonnel::LesDeux;

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
    public function getNumeroPersonnel(): ?string { return $this->numeroPersonnel; }
    public function setNumeroPersonnel(string $numeroPersonnel): static { $this->numeroPersonnel = strtoupper(trim($numeroPersonnel)); return $this; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = trim($nom); return $this; }
    public function getRole(): RolePersonnel { return $this->role; }
    public function setRole(RolePersonnel $role): static { $this->role = $role; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
    public function __toString(): string { return sprintf('%s — %s', $this->numeroPersonnel, $this->nom); }
}
