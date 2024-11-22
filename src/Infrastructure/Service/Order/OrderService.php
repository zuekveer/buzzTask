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
        int $ticketAdultPrice,
        int $ticketAdultQuantity,
        int $ticketKidPrice,
        int $ticketKidQuantity
    ): void {
        $equalPrice = ($ticketAdultPrice * $ticketAdultQuantity) + ($ticketKidPrice * $ticketKidQuantity);
        $barcode = null;

        // Start a transaction for better data integrity
        $this->em->beginTransaction();

        try {
            // Generate a unique barcode
            do {
                $barcode = $this->generateUniqueBarcode();
                $response = $this->mockApiService->mockApiRequest('https://api.site.com/book', [
                    'event_id' => $eventId,
                    'event_date' => $eventDate,
                    'ticket_adult_price' => $ticketAdultPrice,
                    'ticket_adult_quantity' => $ticketAdultQuantity,
                    'ticket_kid_price' => $ticketKidPrice,
                    'ticket_kid_quantity' => $ticketKidQuantity,
                    'barcode' => $barcode,
                ]);
            } while (isset($response['error']));

            // Retry logic for approving the order
            $this->approveOrder($barcode);

            // Create and save the order
            $order = (new Order())
                ->setEventId($eventId)
                ->setEventDate($eventDate)
                ->setTicketAdultPrice($ticketAdultPrice)
                ->setTicketAdultQuantity($ticketAdultQuantity)
                ->setTicketKidPrice($ticketKidPrice)
                ->setTicketKidQuantity($ticketKidQuantity)
                ->setBarcode($barcode)
                ->setEqualPrice($equalPrice)
                ->setCreatedAt(new \DateTimeImmutable());

            $this->orderRepository->save($order);

            // Commit the transaction
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Failed to create order', ['exception' => $e]);
            throw $e; // Rethrow the exception after logging
        }
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = bin2hex(random_bytes(6)); // Generate a random 12-character barcode
        } while ($this->orderRepository->findByBarcode($barcode));

        return $barcode;
    }

    private function approveOrder(string $barcode): void
    {
        $maxRetries = 3;
        $retryCount = 0;
        $approveResponse = null;

        while ($retryCount < $maxRetries) {
            $approveResponse = $this->mockApiService->mockApiRequest('https://api.site.com/approve', ['barcode' => $barcode]);

            if (!isset($approveResponse['error'])) {
                break; // Exit loop if approval is successful
            }

            $retryCount++;
            if ($retryCount === $maxRetries) {
                throw new \RuntimeException("Failed to approve order after {$maxRetries} attempts: {$approveResponse['error']}");
            }

            // Optional delay before retrying
            sleep(1);
        }
    }
}
