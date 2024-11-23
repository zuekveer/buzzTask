<?php

declare(strict_types=1);

namespace App\Domain\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $id;

    #[ORM\OneToMany(targetEntity: 'App\Domain\Entity\Ticket\Ticket', mappedBy: 'order', cascade: ['persist'])]
    private $tickets;  // relation to tickets

    #[ORM\Column(type: 'float', precision: 10, scale: 2)]
    private float $totalPrice;

    #[ORM\Column(type: 'integer')]
    private int $eventId;

    #[ORM\Column(type: 'string', length: 10)]
    private string $eventDate;

    #[ORM\Column(type: 'integer')]
    private int $ticketAdultPrice;    // Added Adult price field

    #[ORM\Column(type: 'integer')]
    private int $ticketAdultQuantity; // Added Adult quantity field

    #[ORM\Column(type: 'integer')]
    private int $ticketKidPrice;    // Added Kid price field

    #[ORM\Column(type: 'integer')]
    private int $ticketKidQuantity; // Added Kid quantity field

    #[ORM\Column(type: 'integer')]
    private int $ticketVipPrice; // Added VIP price field

    #[ORM\Column(type: 'integer')]
    private int $ticketVipQuantity; // Added VIP quantity field

    #[ORM\Column(type: 'string', length: 120, unique: true)]
    private string $barcode;

    #[ORM\Column(type: 'integer')]
    private int $equalPrice;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

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

    public function setTicketVipPrice(int $ticketVipPrice): self
    {
        $this->ticketVipPrice = $ticketVipPrice; // Set VIP price
        return $this;
    }

    public function getTicketVipPrice(): int
    {
        return $this->ticketVipPrice;
    }

    public function setTicketVipQuantity(int $ticketVipQuantity): self
    {
        $this->ticketVipQuantity = $ticketVipQuantity; // Set VIP quantity
        return $this;
    }

    public function getTicketVipQuantity(): int
    {
        return $this->ticketVipQuantity;
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

    /**
     * @return mixed
     */
    public function getTickets(): mixed
    {
        return $this->tickets;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    // New method to calculate total price
    public function calculateTotalPrice(): void
    {
        $this->totalPrice = (
            ($this->ticketAdultPrice * $this->ticketAdultQuantity) +
            ($this->ticketKidPrice * $this->ticketKidQuantity) +
            ($this->ticketVipPrice * $this->ticketVipQuantity)
        );
    }
}
