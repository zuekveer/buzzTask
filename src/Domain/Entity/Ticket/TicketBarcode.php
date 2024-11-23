<?php

namespace App\Domain\Entity\Ticket;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ticket_barcodes')]
class TicketBarcode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: 'App\Domain\Entity\Ticket\Ticket', inversedBy: 'barcodes')]
    #[ORM\JoinColumn(name: 'ticket_id', referencedColumnName: 'id')]
    private Ticket $ticket;


    #[ORM\Column(type: 'string', length: 120, unique: true)]
    private string $barcode;

    public function getId(): int
    {
        return $this->id;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
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
}
