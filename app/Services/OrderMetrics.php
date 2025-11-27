<?php

namespace App\Services;

use Illuminate\Support\Collection;

class OrderMetrics
{
    public function __construct(
        protected Collection $orders
    ) {}

    public function totalOrders(): int
    {
        return $this->orders->count();
    }

    protected static function toNumber(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $normalized = str_replace(['.', ' ', "\u{00A0}"], ['', '', ''], $value);
            $normalized = str_replace(',', '.', $normalized);

            return is_numeric($normalized) ? (float) $normalized : 0.0;
        }

        return 0.0;
    }

    public function totalRevenue(): float
    {
        return $this->orders->sum(
            function (array $order) {
                return self::toNumber($order['local_currency_amount'] ?? 0);
        });
    }

    public function totalRevenueUsd(): float
    {
        return $this->orders->sum(
            function (array $order) {
                $local = self::toNumber($order['local_currency_amount'] ?? 0);
                $rate  = isset($order['exchange_rate_USD'])
                    ? (float) $order['exchange_rate_USD']
                    : 0.0;

                if ($rate <= 0) {
                    return 0.0;
                }

                return $local * $rate;
            }
        );
    }

    public function deliveredOrders(): int
    {
        return $this->orders->filter(
            function (array $order) {
                $status = $order['fulfillment_status'] ?? null;
                $statusId = $order['status_id'] ?? null;

                return $status === 'Fully Fulfilled';
            }
        )->count();
    }

    public function deliveryRate(): float
    {
        $total = $this->totalOrders();

        if ($total === 0) {
            return 0.0;
        }

        return ($this->deliveredOrders() / $total) * 100;
    }

    public function uniqueCustomers(): int
    {
        return $this->orders
            ->map(function (array $order) {
                return $order['customer']['id'] ?? null;
            })
            ->filter() 
            ->unique()
            ->count();
    }

    public function averageOrdersPerCustomer(): float
    {
        $customers = $this->uniqueCustomers();

        if ($customers === 0) {
            return 0.0;
        }

        return $this->totalOrders() / $customers;
    }

    public function financialSummary(): array
    {
        $gross = $this->totalRevenue();
        $refundTotal = $this->orders->sum(
            function (array $order) {
                $refunds = $order['refunds'] ?? [];

                return collect($refunds)->sum(
                    function ($refund) {
                        $value = $refund['local_currency_total_amount'] ?? 
                                 $refund['total'] ?? 
                                 $refund['amount'] ?? 0;
                        return self::toNumber($value);
                    }
                );
            }
    );

        $net = $gross - $refundTotal;

        return [
            'gross'   => $gross,
            'refunds' => $refundTotal,
            'net'     => $net,
        ];
    }

    public function refundRate(): float
    {
        $total = max(1, $this->totalOrders());

        $refundedCount = $this->orders->filter(
            function (array $order) {
                $refunds = $order['refunds'] ?? [];
                $lineItems = $order['line_items'] ?? [];

                $hasRefundArray = is_array($refunds) && count($refunds) > 0;

                $hasRefundInLineItems = collect($lineItems)->contains(
                    function ($item) {
                        return ($item['is_refunded'] ?? 0) == 1;
                    }
                );

                return $hasRefundArray || $hasRefundInLineItems;
            }
        )->count();

        return ($refundedCount / $total) * 100;
    }

    public function bestSellingProduct(): ?array
    {
        $lineItems = $this->orders->flatMap(
            function (array $order) {
                return $order['line_items'] ?? [];
        });

        if ($lineItems->isEmpty()) {
            return null;
        }

        $grouped = $lineItems->groupBy(
            function ($item) {
                return $item['title'] ?? $item['name'] ?? 'Desconhecido';
            }
        );

        $ranked = $grouped->map(
            function (Collection $items, string $name) {
                $quantity = $items->sum('quantity');
                $revenue  = $items->sum(
                    function ($item) {
                        return self::toNumber($item['local_currency_item_total_price'] ?? $item['total_price'] ?? 0);
                    }
                );

                return [
                    'name'     => $name,
                    'quantity' => $quantity,
                    'revenue'  => $revenue,
                ];
            }
        );

        return $ranked
            ->sortByDesc('quantity')
            ->values()
            ->first();
    }

    public function ordersTable(): Collection
    {
        return $this->orders->map(
            function (array $order) {
                $customer   = $order['customer'] ?? [];
                $billing    = $order['billing_address'] ?? [];
                $shipping   = $order['shipping_address'] ?? [];

                $customerName =
                    ($customer['first_name'] ?? $billing['first_name'] ?? $shipping['first_name'] ?? '')
                    . ' ' .
                    ($customer['last_name'] ?? $billing['last_name'] ?? $shipping['last_name'] ?? '');

                return [
                    'id'         => $order['id'] ?? null,
                    'order_no'   => $order['order_number'] ?? $order['name'] ?? '',
                    'created_at' => $order['created_at'] ?? null,
                    'customer'   => trim($customerName),
                    'email'      => $order['email'] ?? $order['contact_email'] ?? '',
                    'status'     => $order['status_id'] ?? $order['financial_status'] ?? null,
                    'fulfillment_status' => $order['fulfillment_status'] ?? null,
                    'amount'     => self::toNumber($order['local_currency_amount'] ?? 0),
                    'currency'   => $order['currency'] ?? '',
                    'city'       => $shipping['city'] ?? $billing['city'] ?? null,
                    'country'    => $shipping['country'] ?? $billing['country'] ?? null,
                ];
            }
        );
    }

    protected function calculateTotalUsd(): float
    {
        return $this->orders->sum(function ($order) {
            $value = $order['current_total_price'] ?? 0;
            $value = str_replace(',', '', $value);

            return (float) $value;
        });
    }

    public function toArray(): array
    {
        $summary = $this->financialSummary();

        return [
            'total_orders'                  => $this->totalOrders(),
            'total_revenue'                 => $summary['gross'], 
            'total_revenue_usd'             => $this->totalRevenueUsd(),
            'delivered_orders'              => $this->deliveredOrders(),
            'delivery_rate'                 => $this->deliveryRate(),
            'unique_customers'              => $this->uniqueCustomers(),
            'average_orders_per_customer'   => $this->averageOrdersPerCustomer(),
            'net_revenue'                   => $summary['net'],
            'refund_total'                  => $summary['refunds'],
            'refund_rate'                   => $this->refundRate(),
            'average_ticket'                => $this->averageTicket(),
        ];
    }


    public function averageTicket(): float
    {
        $totalOrders = max(1, $this->totalOrders());
        return $this->totalRevenue() / $totalOrders;
    }

    public function topProductsByRevenue(int $limit = 5): Collection
    {
        $lineItems = $this->orders->flatMap(
            function (array $order) {
                return $order['line_items'] ?? [];
            }
        );

        $grouped = $lineItems->groupBy(
            function ($item) {
                return $item['title'] ?? $item['name'] ?? 'Desconhecido';
            }
        );

        $ranked = $grouped->map(
            function (Collection $items, string $name) {
                $revenue = $items->sum(
                    function ($item) {
                        return self::toNumber($item['local_currency_item_total_price'] ?? $item['total_price'] ?? 0);
                    }
                );

                $quantity = $items->sum('quantity');

                return [
                    'name'     => $name,
                    'quantity' => $quantity,
                    'revenue'  => $revenue,
                ];
            }
        );

        return $ranked
            ->sortByDesc('revenue')
            ->values()
            ->take($limit);
    }

    public function topCitiesByRevenue(int $limit = 10): Collection
    {
        $grouped = $this->orders->groupBy(
            function (array $order) {
                $shipping = $order['shipping_address'] ?? [];
                $billing  = $order['billing_address'] ?? [];

                $city    = $shipping['city'] ?? $billing['city'] ?? 'Desconhecida';
                $country = $shipping['country'] ?? $billing['country'] ?? '';

                return $city . '|' . $country;
            }
        );

        $ranked = $grouped->map(
            function (Collection $orders, string $key) {
                [$city, $country] = explode('|', $key);

                $revenue = $orders->sum(
                    function (array $order) {
                        return self::toNumber($order['local_currency_amount'] ?? 0);
                    }
                );

                $count = $orders->count();

                return [
                    'city'    => $city,
                    'country' => $country,
                    'orders'  => $count,
                    'revenue' => $revenue,
                ];
            }
        );

        return $ranked
            ->sortByDesc('revenue')
            ->values()
            ->take($limit);
    }

    public function salesByDay(): array
    {
        $grouped = $this->orders
            ->filter(
                function (array $order) {
                    return !empty($order['created_at'] ?? null);
                }
            )
            ->groupBy(
                function (array $order) {
                    $createdAt = $order['created_at'];
                    return substr($createdAt, 0, 10); // "YYYY-MM-DD"
                }
            );

        $perDay = $grouped->map(
            function (Collection $orders, string $date) {
                $revenue = $orders->sum(
                    function (array $order) {
                        return self::toNumber($order['local_currency_amount'] ?? 0);
                    }
                );

                return [
                    'date'    => $date,
                    'revenue' => $revenue,
                ];
            }
        );

        return $perDay
            ->sortBy('date')
            ->values()
            ->all();
    }
}
