<?php

declare(strict_types=1);

namespace App\Domain\Repository\Order;

use App\Domain\Entity\Order\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findByBarcode(string $barcode): ?Order;
}
