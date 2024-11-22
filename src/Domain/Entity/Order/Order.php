<?php

declare(strict_types=1);

namespace App\Domain\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\HasLifecycleCallbacks]
class Order
{

    #[ORM\OneToMany(targetEntity: 'App\Domain\Entity\Ticket\Ticket', mappedBy: 'order')]
    private iterable $tickets;  // relation to tickets

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $id;

    #[ORM\Column(type: 'integer')]
    private int $eventId;

    #[ORM\Column(type: 'string', length: 10)]
    private string $eventDate;

    #[ORM\Column(type: 'integer')]
    private int $ticketAdultPrice;

    #[ORM\Column(type: 'integer')]
    private int $ticketAdultQuantity;

    #[ORM\Column(type: 'integer')]
    private int $ticketKidPrice;

    #[ORM\Column(type: 'integer')]
    private int $ticketKidQuantity;

    #[ORM\Column(type: 'string', length: 120, unique: true)]
    private string $barcode;

    #[ORM\Column(type: 'integer')]
    private int $equalPrice;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setEventId(int $eventId): self
    {
        $this->eventId = $eventId;
        return $this;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventDate(string $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function setTicketAdultPrice(int $ticketAdultPrice): self
    {
        $this->ticketAdultPrice = $ticketAdultPrice;
        return $this;
    }

    public function getTicketAdultPrice(): int
    {
        return $this->ticketAdultPrice;
    }

    public function setTicketAdultQuantity(int $ticketAdultQuantity): self
    {
        $this->ticketAdultQuantity = $ticketAdultQuantity;
        return $this;
    }

    public function getTicketAdultQuantity(): int
    {
        return $this->ticketAdultQuantity;
    }

    public function setTicketKidPrice(int $ticketKidPrice): self
    {
        $this->ticketKidPrice = $ticketKidPrice;
        return $this;
    }

    public function getTicketKidPrice(): int
    {
        return $this->ticketKidPrice;
    }

    public function setTicketKidQuantity(int $ticketKidQuantity): self
    {
        $this->ticketKidQuantity = $ticketKidQuantity;
        return $this;
    }

    public function getTicketKidQuantity(): int
    {
        return $this->ticketKidQuantity;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setEqualPrice(int $equalPrice): self
    {
        $this->equalPrice = $equalPrice;
        return $this;
    }

    public function getEqualPrice(): int
    {
        return $this->equalPrice;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
