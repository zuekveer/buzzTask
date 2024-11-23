<?php

declare(strict_types=1);

namespace App\Presentation\Http\App\Controller\Order;

use App\Infrastructure\Service\Order\OrderService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private OrderService $orderService;
    private LoggerInterface $logger;

    public function __construct(OrderService $orderService, LoggerInterface $logger)
    {
        $this->orderService = $orderService;
        $this->logger = $logger;
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();

        // Ensure required fields are present in the request data
        if (!isset($data['eventId'], $data['eventDate'], $data['tickets'])) {
            return new JsonResponse(['error' => 'Missing required fields.'], Response::HTTP_BAD_REQUEST);
        }

        $tickets = $data['tickets'];

        // Ensure prices and quantities for all ticket types are provided
        foreach (['adult', 'kid', 'vip'] as $type) {
            if (!isset($tickets[$type]['price'], $tickets[$type]['quantity'])) {
                return new JsonResponse(['error' => "Missing price or quantity for $type tickets."], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            // Pass the data to the OrderService for processing
            $this->orderService->createOrder(
                (int) $data['eventId'],
                $data['eventDate'],
                $tickets
            );

            return new JsonResponse(['message' => 'Order created successfully.'], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('Order creation failed', ['exception' => $e, 'data' => $data]);

            return new JsonResponse(['error' => 'Failed to create order.', 'details' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
