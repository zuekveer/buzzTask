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

        // Log input data for debugging
        $this->logger->info('Creating order with data', [
            'eventId' => $eventId,
            'eventDate' => $eventDate,
            'ticketAdultPrice' => $ticketAdultPrice,
            'ticketAdultQuantity' => $ticketAdultQuantity,
            'ticketKidPrice' => $ticketKidPrice,
            'ticketKidQuantity' => $ticketKidQuantity,
            'equalPrice' => $equalPrice
        ]);

        // Start a transaction for better data integrity
        $this->em->beginTransaction();

        try {
            // Generate a unique barcode and attempt booking
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
            } while (isset($response['error']) && $response['error'] === 'barcode already exists');

            // After successful booking, try approving the order
            $this->approveOrder($barcode);

            // Create the Order object
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

            // Log the created order data for debugging
            $this->logger->info('Order created', ['order' => $order]);

            // Save the Order to the repository
            $this->orderRepository->save($order);

            // Commit the transaction
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Failed to create order', [
                'exception' => $e,
                'eventId' => $eventId,
                'eventDate' => $eventDate,
                'ticketAdultPrice' => $ticketAdultPrice,
                'ticketAdultQuantity' => $ticketAdultQuantity,
                'ticketKidPrice' => $ticketKidPrice,
                'ticketKidQuantity' => $ticketKidQuantity,
                'barcode' => $barcode,
            ]);
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

        while ($retryCount < $maxRetries) {
            $approveResponse = $this->mockApiService->mockApiRequest('https://api.site.com/approve', ['barcode' => $barcode]);

            if (!isset($approveResponse['error'])) {
                return; // Approval successful
            }

            $this->logger->warning('Approval attempt failed', [
                'barcode' => $barcode,
                'attempt' => $retryCount + 1,
                'error' => $approveResponse['error'],
            ]);

            $retryCount++;
            sleep(1); // Optional: Add delay between retries
        }

        // Log and throw error only if all attempts fail
        $this->logger->error('Order approval failed after retries', ['barcode' => $barcode]);
        throw new \RuntimeException("Failed to approve order after {$maxRetries} attempts: no seats");
    }
}
