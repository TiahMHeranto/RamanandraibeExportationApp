<?php

namespace App\Entity;

use App\Enum\ShipmentStatus;
use App\Repository\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'shipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(enumType: ShipmentStatus::class)]
    private ShipmentStatus $status = ShipmentStatus::Draft;

    #[ORM\Column(length: 120)]
    private ?string $originPort = null;

    #[ORM\Column(length: 120)]
    private ?string $destinationPort = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $departureDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $arrivalDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, ShipmentLine> */
    #[ORM\OneToMany(targetEntity: ShipmentLine::class, mappedBy: 'shipment', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lines;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): ShipmentStatus
    {
        return $this->status;
    }

    public function setStatus(ShipmentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOriginPort(): ?string
    {
        return $this->originPort;
    }

    public function setOriginPort(string $originPort): static
    {
        $this->originPort = $originPort;

        return $this;
    }

    public function getDestinationPort(): ?string
    {
        return $this->destinationPort;
    }

    public function setDestinationPort(string $destinationPort): static
    {
        $this->destinationPort = $destinationPort;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeImmutable
    {
        return $this->departureDate;
    }

    public function setDepartureDate(?\DateTimeImmutable $departureDate): static
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(?\DateTimeImmutable $arrivalDate): static
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return Collection<int, ShipmentLine> */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(ShipmentLine $line): static
    {
        if (!$this->lines->contains($line)) {
            $this->lines->add($line);
            $line->setShipment($this);
        }

        return $this;
    }

    public function removeLine(ShipmentLine $line): static
    {
        if ($this->lines->removeElement($line) && $line->getShipment() === $this) {
            $line->setShipment(null);
        }

        return $this;
    }

    public function getTotalAmount(): string
    {
        $total = 0.0;
        foreach ($this->lines as $line) {
            $total += (float) $line->getLineTotal();
        }

        return number_format($total, 2, '.', '');
    }
}
