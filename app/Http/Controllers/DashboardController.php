<?php

namespace App\Http\Controllers;

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

        return view('dashboard.index', [
            'orders' => $orders,
        ]);
    }
}
