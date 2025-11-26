<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected string $endpoint = 'https://dev-crm.ogruposix.com/candidato-teste-pratico-backend-dashboard/test-orders';

    public function getOrders(): Collection
    {
        return Cache::remember('test_orders', now()->addMinutes(120), function () {
            $response = Http::withoutVerifying()->timeout(30)->get($this->endpoint);

            if ($response->failed()) {
                Log::error('Erro ao buscar pedidos na API do teste', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return collect();
            }

            $data = $response->json();

            $ordersWrapped = $data['orders'] ?? [];

            return collect($ordersWrapped)
                ->map(function ($item) {
                    return $item['order'] ?? $item;
                });
        });
    }
}
