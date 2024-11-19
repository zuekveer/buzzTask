<?php

namespace App\Infrastructure\Service\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Repository\Order\OrderRepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderService
{
    private OrderRepositoryInterface $orderRepository;
    private HttpClientInterface $httpClient;

    public function __construct(OrderRepositoryInterface $orderRepository, HttpClientInterface $httpClient)
    {
        $this->orderRepository = $orderRepository;
        $this->httpClient = $httpClient;
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

        do {
            $barcode = $this->generateUniqueBarcode();
            $response = $this->mockApiRequest('https://api.site.com/book', [
                'event_id' => $eventId,
                'event_date' => $eventDate,
                'ticket_adult_price' => $ticketAdultPrice,
                'ticket_adult_quantity' => $ticketAdultQuantity,
                'ticket_kid_price' => $ticketKidPrice,
                'ticket_kid_quantity' => $ticketKidQuantity,
                'barcode' => $barcode,
            ]);
        } while ($response['error'] ?? false);

        $approveResponse = $this->mockApiRequest('https://api.site.com/approve', [
            'barcode' => $barcode,
        ]);

        if (isset($approveResponse['error'])) {
            throw new \RuntimeException("Failed to approve order: {$approveResponse['error']}");
        }

        $order = new Order();
        $order->setEventId($eventId)
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

    private function mockApiRequest(string $url, array $data): array
    {
        // Simulate API call (replace with actual HTTP call in production)
        $responses = [
            ['message' => 'order successfully booked'],
            ['error' => 'barcode already exists'],
        ];

        return $responses[array_rand($responses)];
    }
}
