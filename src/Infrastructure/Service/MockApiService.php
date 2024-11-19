<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

class MockApiService
{
    public function mockApiRequest(string $url, array $data): array
    {
        $responses = match ($url) {
            'https://api.site.com/book' => [
                ['message' => 'order successfully booked'],
                ['error' => 'barcode already exists'],
            ],
            'https://api.site.com/approve' => [
                ['message' => 'order successfully approved'],
                ['error' => 'event cancelled'],
                ['error' => 'no tickets'],
                ['error' => 'no seats'],
                ['error' => 'fan removed'],
            ],
            default => [['error' => 'unknown endpoint']],
        };

        return $responses[array_rand($responses)];
    }
}
