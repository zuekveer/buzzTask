<?php

namespace App\Domain\Entity\Ticket;

use App\Domain\Entity\Order\Order;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'App\Domain\Entity\Order\Order', inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private Order $order;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\OneToMany(targetEntity: 'App\Domain\Entity\Ticket\TicketBarcode', mappedBy: 'ticket')]
    private iterable $barcodes;  // relation to ticket barcodes

    // Getter for id
    public function getId(): int
    {
        return $this->id;
    }

    // Setter and Getter for Order
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    // Setter and Getter for Type
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    // Setter and Getter for Price
    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    // Setter and Getter for Quantity
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    // Getter for Barcodes
    public function getBarcodes(): iterable
    {
        return $this->barcodes;
    }

    // Setter for Barcodes
    public function setBarcodes(iterable $barcodes): self
    {
        $this->barcodes = $barcodes;
        return $this;
    }
}
