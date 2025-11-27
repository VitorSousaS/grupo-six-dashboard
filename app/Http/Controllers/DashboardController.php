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

        return view('dashboard.index', [
            'orders'          => $ordersTable,
            'metrics'         => $metrics->toArray(),
            'bestProduct'     => $metrics->bestSellingProduct(),
            'topProducts'     => $metrics->topProductsByRevenue(),
            'topCities'       => $metrics->topCitiesByRevenue(),
            'salesByDay'      => $metrics->salesByDay(),
            'selectedStatus'  => $selectedStatus,
            'allowedStatuses' => $allowedStatuses,
        ]);
    }
}
