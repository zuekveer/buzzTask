<?php

namespace App\Infrastructure\Service\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Repository\Order\OrderRepositoryInterface;
use App\Infrastructure\Service\MockApiService;

class OrderService
{
    private OrderRepositoryInterface $orderRepository;
    private MockApiService $mockApiService; // Inject MockApiService

    public function __construct(OrderRepositoryInterface $orderRepository, MockApiService $mockApiService)
    {
        $this->orderRepository = $orderRepository;
        $this->mockApiService = $mockApiService; // Injected service
    }

    public function createOrder(
        int $eventId,
        string $eventDate,
        int $ticketAdultPrice,
        int $ticketAdultQuantity,
        int $ticketKidPrice,
        int $ticketKidQuantity
    ): void {
        $equalPrice = ($ticketAdultPrice * $ticketAdultQuantity) + ($ticketKidPrice * $ticketKidQuantity);

        $barcode = null;
        do {
            $barcode = $this->generateUniqueBarcode();
            $response = $this->mockApiService->mockApiRequest('https://api.site.com/book', [ // Using MockApiService
                'event_id' => $eventId,
                'event_date' => $eventDate,
                'ticket_adult_price' => $ticketAdultPrice,
                'ticket_adult_quantity' => $ticketAdultQuantity,
                'ticket_kid_price' => $ticketKidPrice,
                'ticket_kid_quantity' => $ticketKidQuantity,
                'barcode' => $barcode,
            ]);
        } while (isset($response['error']));

        $approveResponse = $this->mockApiService->mockApiRequest('https://api.site.com/approve', [ // Using MockApiService
            'barcode' => $barcode,
        ]);

        if (isset($approveResponse['error'])) {
            throw new \RuntimeException("Failed to approve order: {$approveResponse['error']}");
        }

        $order = (new Order())
            ->setEventId($eventId)
            ->setEventDate($eventDate)
            ->setTicketAdultPrice($ticketAdultPrice)
            ->setTicketAdultQuantity($ticketAdultQuantity)
            ->setTicketKidPrice($ticketKidPrice)
            ->setTicketKidQuantity($ticketKidQuantity)
            ->setBarcode($barcode)
            ->setEqualPrice($equalPrice)
            ->setCreated(new \DateTimeImmutable());

        $this->orderRepository->save($order);
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = bin2hex(random_bytes(6));
        } while ($this->orderRepository->findByBarcode($barcode));

        return $barcode;
    }
}
