<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\OrderMetrics;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $orders         = $this->orderService->getOrders();
        $metrics        = new OrderMetrics($orders);
        $metricsArray   = $metrics->toArray();
        $ordersTable    = $metrics->ordersTable();
         
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
            $ordersTable = $ordersTable->filter(
                function (array $order) use ($selectedStatus) {
                    return ($order['status'] ?? '') === $selectedStatus;
                }
            );
        }

        $search = trim((string) $request->get('search', ''));

        if ($search !== '') {
            $term = mb_strtolower($search);

            $ordersTable = $ordersTable->filter(function (array $order) use ($term) {
                return str_contains(mb_strtolower((string) $order['customer']), $term)
                    || str_contains(mb_strtolower((string) $order['email']), $term)
                    || str_contains((string) $order['order_no'], $term)
                    || str_contains((string) $order['id'], $term);
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(5, min($perPage, 100)); 
        $currentPage = Paginator::resolveCurrentPage() ?: 1;

        $total = $ordersTable->count();

        $currentPageItems = $ordersTable
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $paginatedOrders = new LengthAwarePaginator(
            $currentPageItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
                'fragment' => 'orders-table',
            ]
        );

        $metricsArray['total_revenue_formatted'] = number_format($metricsArray['total_revenue'], 2, ',', '.');
        $metricsArray['total_revenue_usd_formatted'] = number_format($metricsArray['total_revenue_usd'], 2, '.', ',');
        
        $metricsArray['delivery_rate_formatted'] = number_format($metricsArray['delivery_rate'], 2, ',', '.') . '%';
        $metricsArray['average_orders_per_customer_formatted'] = number_format($metricsArray['average_orders_per_customer'], 2, ',', '.');
        
        $metricsArray['gross_formatted']   = number_format($metricsArray['total_revenue'], 2, ',', '.');
        $metricsArray['refunds_formatted'] = number_format($metricsArray['refund_total'], 2, ',', '.');
        $metricsArray['net_formatted']     = number_format($metricsArray['net_revenue'], 2, ',', '.');

        $metricsArray['refund_rate_formatted'] = number_format($metricsArray['refund_rate'], 2, ',', '.') . '%';
        
        $metricsArray['delivered_refunded_rate_formatted'] = number_format($metricsArray['delivered_refunded_rate'], 2, ',', '.') . '%';

        return view('dashboard.index', [
            'orders'          => $paginatedOrders,
            'search'          => $search,
            'perPage'         => $perPage,
            
            'metrics'         => $metricsArray,
            'bestProduct'     => $metrics->bestSellingProduct(),
            'topProducts'     => $metrics->topProductsByRevenue(),
            'topCities'       => $metrics->topCitiesByRevenue(),
            'salesByDay'      => $metrics->salesByDay(),
            'selectedStatus'  => $selectedStatus,
            'allowedStatuses' => $allowedStatuses,
            'refundReasons'   => $metrics->refundReasonsSummary(),
        ]);
    }
}
