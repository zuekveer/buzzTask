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

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="Creates a new order with tickets and returns a success message.",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="eventId", type="integer", description="The ID of the event"),
     *             @OA\Property(property="eventDate", type="string", format="date", description="The date of the event"),
     *             @OA\Property(
     *                 property="tickets",
     *                 type="object",
     *                 @OA\Property(property="adult", type="object",
     *                     @OA\Property(property="price", type="integer", description="Price of an adult ticket"),
     *                     @OA\Property(property="quantity", type="integer", description="Quantity of adult tickets")
     *                 ),
     *                 @OA\Property(property="kid", type="object",
     *                     @OA\Property(property="price", type="integer", description="Price of a kid ticket"),
     *                     @OA\Property(property="quantity", type="integer", description="Quantity of kid tickets")
     *                 ),
     *                 @OA\Property(property="vip", type="object",
     *                     @OA\Property(property="price", type="integer", description="Price of a VIP ticket"),
     *                     @OA\Property(property="quantity", type="integer", description="Quantity of VIP tickets")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Order created successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Missing required fields.")
     *         )
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        // Using toArray() to parse JSON body
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

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get an order by ID",
     *     description="Fetches an order by its ID.",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="eventId", type="integer", example=101),
     *             @OA\Property(property="eventDate", type="string", format="date", example="2023-12-01"),
     *             @OA\Property(property="totalPrice", type="float", example=150.00),
     *             @OA\Property(property="tickets", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="adult"),
     *                     @OA\Property(property="price", type="integer", example=50),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Order not found.")
     *         )
     *     )
     * )
     */
    public function get(int $id): JsonResponse
    {
        try {
            // Delegate the logic to OrderService
            $order = $this->orderService->getOrderById($id);

            if (!$order) {
                return new JsonResponse(['error' => 'Order not found.'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse(['order' => $order], Response::HTTP_OK);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch order', ['exception' => $e, 'orderId' => $id]);

            return new JsonResponse(
                ['error' => 'Failed to fetch order.', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
