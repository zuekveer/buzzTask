<?php

declare(strict_types=1);

namespace App\Presentation\Http\App\Controller\Order;

use App\Domain\Repository\Order\OrderRepositoryInterface;
use App\Infrastructure\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private OrderService $orderService;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderService $orderService, OrderRepositoryInterface $orderRepository)
    {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }

    public function create(Request $request): JsonResponse
    {
        // Using toArray() to parse JSON body
        $data = $request->toArray();

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
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return new JsonResponse(['error' => 'Order not found.'], Response::HTTP_NOT_FOUND);
            }

            // Get barcodes for the order (through related tickets)
            $barcodes = [];
            foreach ($order->getTickets() as $ticket) {
                foreach ($ticket->getBarcodes() as $barcode) {
                    $barcodes[] = $barcode->getBarcode();
                }
            }

            return new JsonResponse([
                'id' => $order->getId(),
                'event_id' => $order->getEventId(),
                'event_date' => $order->getEventDate(),
                'ticket_adult_price' => $order->getTicketAdultPrice(),
                'ticket_adult_quantity' => $order->getTicketAdultQuantity(),
                'ticket_kid_price' => $order->getTicketKidPrice(),
                'ticket_kid_quantity' => $order->getTicketKidQuantity(),
                'barcodes' => $barcodes,  // Fetch and return barcodes
                'equal_price' => $order->getEqualPrice(),
                'created' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
