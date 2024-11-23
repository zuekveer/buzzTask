<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository\Order;

use App\Domain\Entity\Order\Order;
use App\Domain\Repository\Order\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class OrderRepository implements OrderRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
    }

    public function findByBarcode(string $barcode): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy(['barcode' => $barcode]);
    }

    public function find(int $id): ?Order
    {
        // Try to find the order by ID
        try {
            return $this->em->getRepository(Order::class)->find($id);
        } catch (NoResultException $e) {
            // If no result found, return null
            return null;
        }
    }
}
