<?php

namespace App\Services;

use Illuminate\Support\Collection;

class OrderMetrics
{
    public function __construct(
        protected Collection $orders
    ) {}

    /** Total de pedidos */
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

    public function deliveredOrders(): int
    {
        return $this->orders->filter(
            function (array $order) {
                $status = $order['fulfillment_status'] ?? null;
                $statusId = $order['status_id'] ?? null;

                return $status === 'Fully Fulfilled' || $statusId === 'Fulfilled';
            }
        )->count();
    }

    public function uniqueCustomers(): int
    {
        return $this->orders->map(
            function (array $order) {
                $customer = $order['customer'] ?? null;
                if (is_array($customer) && isset($customer['id'])) {
                    return 'id:' . $customer['id'];
                }

                return '';
            }
        )
        ->unique()
        ->count();
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

    /** Tabela de pedidos (formato simples pra view) */
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

    public function toArray(): array
    {
        $summary = $this->financialSummary();

        return [
            'total_orders'     => $this->totalOrders(),
            'total_revenue'    => $summary['gross'],
            'net_revenue'      => $summary['net'],
            'refund_total'     => $summary['refunds'],
            'delivered_orders' => $this->deliveredOrders(),
            'unique_customers' => $this->uniqueCustomers(),
            'refund_rate'      => $this->refundRate(),
        ];
    }
}
