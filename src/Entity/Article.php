<?php

namespace App\Entity;

use App\Enum\FamilleArticle;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_ARTICLE_CODE', fields: ['code'])]
#[UniqueEntity(fields: ['code'], message: 'Ce code article existe déjà.')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private ?string $code = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private ?string $libelle = null;

    #[ORM\Column(enumType: FamilleArticle::class)]
    private FamilleArticle $famille = FamilleArticle::Autre;

    #[ORM\Column]
    private bool $actif = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = strtoupper(trim($code));

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = trim($libelle);

        return $this;
    }

    public function getFamille(): FamilleArticle
    {
        return $this->famille;
    }

    public function setFamille(FamilleArticle $famille): static
    {
        $this->famille = $famille;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->libelle;
    }
}
