<?php

declare(strict_types=1);

namespace App\Presentation\Http\App\Controller\Order;

use App\Domain\Repository\Order\OrderRepositoryInterface;
use App\Infrastructure\Service\Order\OrderService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private OrderService $orderService;
    private OrderRepositoryInterface $orderRepository;
    private LoggerInterface $logger; // Declare LoggerInterface

    public function __construct(
        OrderService $orderService,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger // Inject LoggerInterface
    ) {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger; // Assign the logger
    }

    public function create(Request $request): JsonResponse
    {
        // Using toArray() to parse JSON body
        $data = $request->toArray();

        // Validate required fields
        $requiredFields = [
            'eventId',
            'eventDate',
            'ticketAdultPrice',
            'ticketAdultQuantity',
            'ticketKidPrice',
            'ticketKidQuantity',
        ];

        // Check if any required field is missing
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Check if fields are valid
        if (!is_int($data['ticketAdultPrice']) || $data['ticketAdultPrice'] <= 0) {
            return new JsonResponse(['error' => 'Invalid ticketAdultPrice'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_int($data['ticketAdultQuantity']) || $data['ticketAdultQuantity'] < 0) {
            return new JsonResponse(['error' => 'Invalid ticketAdultQuantity'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_int($data['ticketKidPrice']) || $data['ticketKidPrice'] <= 0) {
            return new JsonResponse(['error' => 'Invalid ticketKidPrice'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_int($data['ticketKidQuantity']) || $data['ticketKidQuantity'] < 0) {
            return new JsonResponse(['error' => 'Invalid ticketKidQuantity'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Ensure to handle the proper keys from the JSON data
            $this->orderService->createOrder(
                (int) $data['eventId'],
                $data['eventDate'],
                (int) $data['ticketAdultPrice'],
                (int) $data['ticketAdultQuantity'],
                (int) $data['ticketKidPrice'],
                (int) $data['ticketKidQuantity']
            );

            return new JsonResponse(['message' => 'Order created successfully.'], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            // Log the exception for debugging purposes
            $this->logger->error('Order creation failed', [
                'exception' => $e,
                'data' => $data,
            ]);

            // Return a user-friendly error message
            return new JsonResponse([
                'error' => 'Failed to create order.',
                'details' => $e->getMessage(), // Optional: Provide exception details for debugging
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function get(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return new JsonResponse(['error' => 'Order not found.'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'id' => $order->getId(),
                'event_id' => $order->getEventId(),
                'event_date' => $order->getEventDate(),
                'ticket_adult_price' => $order->getTicketAdultPrice(),
                'ticket_adult_quantity' => $order->getTicketAdultQuantity(),
                'ticket_kid_price' => $order->getTicketKidPrice(),
                'ticket_kid_quantity' => $order->getTicketKidQuantity(),
                'barcodes' => $order->getBarcode(),  // Fetch and return barcodes
                'equal_price' => $order->getEqualPrice(),
                'created' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}

