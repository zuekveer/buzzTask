<?php

declare(strict_types=1);

namespace App\Presentation\Http\App\Controller\Order;

use App\Infrastructure\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->request->all();

        try {
            $this->orderService->createOrder(
                (int) $data['event_id'],
                $data['event_date'],
                (int) $data['ticket_adult_price'],
                (int) $data['ticket_adult_quantity'],
                (int) $data['ticket_kid_price'],
                (int) $data['ticket_kid_quantity']
            );

            return new JsonResponse(['message' => 'Order created successfully.'], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
