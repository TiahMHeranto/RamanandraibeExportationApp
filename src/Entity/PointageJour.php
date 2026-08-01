<?php

namespace App\Entity;

use App\Repository\PointageJourRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PointageJourRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_POINTAGE_DATE_PERSONNEL', fields: ['datePointage', 'personnel'])]
#[UniqueEntity(fields: ['datePointage', 'personnel'], message: 'Pointage déjà enregistré pour ce personnel ce jour.')]
class PointageJour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $datePointage = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Personnel $personnel = null;

    #[ORM\Column]
    private bool $present = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    public function getId(): ?int { return $this->id; }
    public function getDatePointage(): ?\DateTimeImmutable { return $this->datePointage; }
    public function setDatePointage(\DateTimeImmutable $d): static { $this->datePointage = $d; return $this; }
    public function getPersonnel(): ?Personnel { return $this->personnel; }
    public function setPersonnel(?Personnel $p): static { $this->personnel = $p; return $this; }
    public function isPresent(): bool { return $this->present; }
    public function setPresent(bool $p): static { $this->present = $p; return $this; }
    public function getObservations(): ?string { return $this->observations; }
    public function setObservations(?string $o): static { $this->observations = $o; return $this; }
}
