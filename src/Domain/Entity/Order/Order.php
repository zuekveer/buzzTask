<?php

declare(strict_types=1);

namespace App\Domain\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true)]
    private int $id;

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

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $created;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function setEventDate(string $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getTicketAdultPrice(): int
    {
        return $this->ticketAdultPrice;
    }

    public function setTicketAdultPrice(int $ticketAdultPrice): void
    {
        $this->ticketAdultPrice = $ticketAdultPrice;
    }

    public function getTicketAdultQuantity(): int
    {
        return $this->ticketAdultQuantity;
    }

    public function setTicketAdultQuantity(int $ticketAdultQuantity): void
    {
        $this->ticketAdultQuantity = $ticketAdultQuantity;
    }

    public function getTicketKidPrice(): int
    {
        return $this->ticketKidPrice;
    }

    public function setTicketKidPrice(int $ticketKidPrice): void
    {
        $this->ticketKidPrice = $ticketKidPrice;
    }

    public function getTicketKidQuantity(): int
    {
        return $this->ticketKidQuantity;
    }

    public function setTicketKidQuantity(int $ticketKidQuantity): void
    {
        $this->ticketKidQuantity = $ticketKidQuantity;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): void
    {
        $this->barcode = $barcode;
    }

    public function getEqualPrice(): int
    {
        return $this->equalPrice;
    }

    public function setEqualPrice(int $equalPrice): void
    {
        $this->equalPrice = $equalPrice;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }
}
