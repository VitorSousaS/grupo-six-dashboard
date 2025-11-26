<?php

namespace App\Http\Controllers;

use App\Services\OrderMetrics;
use App\Services\OrderService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(): View
    {
        $orders = $this->orderService->getOrders();
        $metrics = new OrderMetrics($orders);

        return view('dashboard.index', [
            'orders'          => $metrics->ordersTable(),
            'metrics'         => $metrics->toArray(),
            'bestProduct'     => $metrics->bestSellingProduct(),
            'topProducts'   => $metrics->topProductsByRevenue(),
            'topCities'     => $metrics->topCitiesByRevenue(),
            'salesByDay'    => $metrics->salesByDay()
        ]);
    }
}
