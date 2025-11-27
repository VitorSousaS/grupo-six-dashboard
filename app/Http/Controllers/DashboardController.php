<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\OrderMetrics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $orders = $this->orderService->getOrders();
        $metrics = new OrderMetrics($orders);
        $metricsArray = $metrics->toArray();

        $ordersTable = $metrics->ordersTable();
        $selectedStatus = $request->query('status', 'all');

        $allowedStatuses = [
            'all'                => 'Todos',
            'Fulfilled'          => 'Fulfilled',
            'Partially Fulfilled'=> 'Partially Fulfilled',
            'Unfulfilled'        => 'Unfulfilled',
            'Refunded'           => 'Refunded',
        ];

        if (! array_key_exists($selectedStatus, $allowedStatuses)) {
            $selectedStatus = 'all';
        }

        if ($selectedStatus !== 'all') {
            $ordersTable = $ordersTable
                ->filter(fn (array $order) => ($order['status'] ?? '') === $selectedStatus)
                ->values();
        }

        $metricsArray['total_revenue_formatted'] = number_format($metricsArray['total_revenue'], 2, ',', '.');
        $metricsArray['total_revenue_usd_formatted'] = number_format($metricsArray['total_revenue_usd'], 2, '.', ',');
        $metricsArray['delivery_rate_formatted'] = number_format($metricsArray['delivery_rate'], 2, ',', '.') . '%';
        $metricsArray['average_orders_per_customer_formatted'] = number_format($metricsArray['average_orders_per_customer'], 2, ',', '.');

        return view('dashboard.index', [
            'orders'          => $ordersTable,
            'metrics'         => $metricsArray,
            'bestProduct'     => $metrics->bestSellingProduct(),
            'topProducts'     => $metrics->topProductsByRevenue(),
            'topCities'       => $metrics->topCitiesByRevenue(),
            'salesByDay'      => $metrics->salesByDay(),
            'selectedStatus'  => $selectedStatus,
            'allowedStatuses' => $allowedStatuses,
        ]);
    }
}
