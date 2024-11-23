<?php

namespace App\Infrastructure\Service\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Repository\Order\OrderRepositoryInterface;
use App\Infrastructure\Service\MockApiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class OrderService
{
    private OrderRepositoryInterface $orderRepository;
    private MockApiService $mockApiService;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MockApiService $mockApiService,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->mockApiService = $mockApiService;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function createOrder(
        int $eventId,
        string $eventDate,
        array $tickets
    ): void {
        // Extract ticket details
        $ticketAdultPrice = $tickets['adult']['price'] ?? null;
        $ticketAdultQuantity = $tickets['adult']['quantity'] ?? 0;
        $ticketKidPrice = $tickets['kid']['price'] ?? null;
        $ticketKidQuantity = $tickets['kid']['quantity'] ?? 0;
        $ticketVipPrice = $tickets['vip']['price'] ?? null;
        $ticketVipQuantity = $tickets['vip']['quantity'] ?? 0;

        if ($ticketAdultPrice === null || $ticketKidPrice === null || $ticketVipPrice === null) {
            throw new \InvalidArgumentException('Ticket prices cannot be null.');
        }

        // Calculate the total price based on quantities and prices
        $totalPrice = $this->calculateTotalPrice(
            $ticketAdultPrice, $ticketAdultQuantity,
            $ticketKidPrice, $ticketKidQuantity,
            $ticketVipPrice, $ticketVipQuantity
        );

        $equalPrice = $totalPrice; // Set equalPrice to the calculated total price

        $barcode = null;

        $this->logger->info('Creating order with data', [
            'eventId' => $eventId,
            'eventDate' => $eventDate,
            'tickets' => $tickets,
            'totalPrice' => $totalPrice,
            'equalPrice' => $equalPrice,
        ]);

        $this->em->beginTransaction();

        try {
            // Generate unique barcode and book
            do {
                $barcode = $this->generateUniqueBarcode();
                $response = $this->mockApiService->mockApiRequest('https://api.site.com/book', [
                    'event_id' => $eventId,
                    'event_date' => $eventDate,
                    'tickets' => $tickets,
                    'barcode' => $barcode,
                ]);
            } while (isset($response['error']) && $response['error'] === 'barcode already exists');

            // Approve the order
            $this->approveOrder($barcode);

            // Create Order Entity
            $order = (new Order())
                ->setEventId($eventId)
                ->setEventDate($eventDate)
                ->setTicketAdultPrice($ticketAdultPrice)
                ->setTicketAdultQuantity($ticketAdultQuantity)
                ->setTicketKidPrice($ticketKidPrice)
                ->setTicketKidQuantity($ticketKidQuantity)
                ->setTicketVipPrice($ticketVipPrice)
                ->setTicketVipQuantity($ticketVipQuantity)
                ->setBarcode($barcode)
                ->setTotalPrice($totalPrice)
                ->setEqualPrice($equalPrice)
                ->setCreatedAt(new \DateTimeImmutable());

            $this->orderRepository->save($order);
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Failed to create order', [
                'exception' => $e,
                'tickets' => $tickets,
            ]);
            throw $e;
        }
    }

    private function calculateTotalPrice(
        int $ticketAdultPrice, int $ticketAdultQuantity,
        int $ticketKidPrice, int $ticketKidQuantity,
        int $ticketVipPrice, int $ticketVipQuantity
    ): float {
        return (
            ($ticketAdultPrice * $ticketAdultQuantity) +
            ($ticketKidPrice * $ticketKidQuantity) +
            ($ticketVipPrice * $ticketVipQuantity)
        );
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = bin2hex(random_bytes(6));
        } while ($this->orderRepository->findByBarcode($barcode));

        return $barcode;
    }

    private function approveOrder(string $barcode): void
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            $response = $this->mockApiService->mockApiRequest('https://api.site.com/approve', ['barcode' => $barcode]);

            if (!isset($response['error'])) {
                return;
            }

            $this->logger->warning('Approval attempt failed', [
                'barcode' => $barcode,
                'attempt' => $retryCount + 1,
                'error' => $response['error'],
            ]);

            $retryCount++;
            sleep(1);
        }

        throw new \RuntimeException("Failed to approve order after {$maxRetries} attempts: no seats");
    }
}
