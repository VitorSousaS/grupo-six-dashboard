@extends('layouts.app')

@section('title', 'Dashboard – Teste Grupo Six')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-800">
            Visão geral
        </h2>
        <p class="text-sm text-slate-500">
            Métricas calculadas a partir dos últimos {{ $metrics['total_orders'] ?? 0 }} pedidos retornados pela API.
        </p>
    </div>

    @if(!empty($salesByDay))
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">
                Faturamento por dia
            </h3>
            <p class="text-xs text-slate-500 mb-3">
                Soma de <code>local_currency_amount</code> agrupada pela data de criação do pedido.
            </p>
            <div class="h-64">
                <canvas id="salesByDayChart"></canvas>
            </div>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total de pedidos</p>
            <p class="mt-2 text-2xl font-semibold">
                {{ $metrics['total_orders'] }}
            </p>
        </div>

        <div class="bg-emerald-50 border-l-4 border-emerald-600 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-emerald-800">
                Receita Total
            </h3>

            <div class="mt-2 space-y-1">
                <p class="text-2xl font-bold text-emerald-900">
                    R$ {{ $metrics['total_revenue_formatted'] }}
                </p>

                <p class="text-sm font-medium text-emerald-800">
                    ≈ US$ {{ $metrics['total_revenue_usd_formatted'] }}
                </p>
            </div>
        </div>

        <div class="bg-sky-50 border-l-4 border-sky-600 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-sky-800">
                Pedidos Entregues
            </h3>

            <div class="mt-2">
                <p class="text-2xl font-bold text-sky-900">
                    {{ $metrics['delivered_orders'] }}
                </p>
                <span class="text-xs font-medium text-sky-800">
                    Taxa de entrega
                </span>
                <span class="text-sm font-semibold text-sky-900">
                    {{ $metrics['delivery_rate_formatted'] }}
                </span>
            </div>
        </div>

        <div class="bg-slate-50 border-l-4 border-slate-600 rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">
                Clientes Únicos
            </h3>

            <div class="mt-2">
                <p class="text-2xl font-bold text-slate-900">
                    {{ $metrics['unique_customers'] }}
                </p>
                <span class="text-xs font-medium text-slate-700">
                    Média de pedidos por cliente
                </span>
                <span class="text-sm font-semibold text-slate-900">
                    {{ $metrics['average_orders_per_customer_formatted'] }}
                </span>
            </div>
        </div>
    </div>

    <section class="mt-6 mb-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">
            Resumo financeiro
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    Faturamento bruto
                </h3>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    R$ {{ $metrics['gross_formatted'] ?? number_format($metrics['total_revenue'], 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-rose-50 border border-rose-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-rose-600 uppercase tracking-wide">
                    Total reembolsado
                </h3>
                <p class="mt-2 text-2xl font-bold text-rose-900">
                    R$ {{ $metrics['refunds_formatted'] ?? number_format($metrics['refund_total'], 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">
                    Receita líquida
                </h3>
                <p class="mt-2 text-2xl font-bold text-emerald-900">
                    R$ {{ $metrics['net_formatted'] ?? number_format($metrics['net_revenue'], 2, ',', '.') }}
                </p>
            </div>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
        @php
            $refundRate = $metrics['refund_rate'] ?? 0;

            if ($refundRate < 10) {
                $refundLevelText  = 'Saudável';
                $refundLevelClass = 'bg-emerald-100 text-emerald-800';
                $cardBorderClass  = 'border-emerald-400';
            } elseif ($refundRate < 20) {
                $refundLevelText  = 'Atenção';
                $refundLevelClass = 'bg-amber-100 text-amber-800';
                $cardBorderClass  = 'border-amber-400';
            } else {
                $refundLevelText  = 'Crítico';
                $refundLevelClass = 'bg-rose-100 text-rose-800';
                $cardBorderClass  = 'border-rose-400';
            }
        @endphp
        <div class="bg-white border-l-4 {{ $cardBorderClass }} rounded-xl p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">
                Taxa de reembolso
            </h3>

            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-2xl font-bold text-slate-900">
                    {{ $metrics['refund_rate_formatted'] ?? (number_format($refundRate, 2, ',', '.') . '%') }}
                </p>

                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $refundLevelClass }}">
                    {{ $refundLevelText }}
                </span>
            </div>
        </div>

        @if(isset($metrics['average_ticket']))
            <div class="bg-white rounded-xl shadow-sm px-4 py-3">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Ticket médio</p>
                <p class="mt-2 text-2xl font-semibold">
                    {{ number_format($metrics['average_ticket'], 2, ',', '.') }}
                </p>
            </div>
        @endif
    </div>

    <div class="grid gap-4 lg:grid-cols-2 mb-8">
        @if($bestProduct)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-semibold text-amber-700 uppercase tracking-wide">
                            Produto mais vendido
                        </h3>
                        <p class="mt-1 text-base font-bold text-amber-900">
                            {{ $bestProduct['name'] }}
                        </p>
                    </div>

                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                        ⭐ Destaque
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-1">
                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Quantidade vendida
                        </p>
                        <p class="text-lg font-bold text-slate-900">
                            {{ $bestProduct['quantity'] }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Receita gerada
                        </p>
                        <p class="text-lg font-bold text-emerald-700">
                            R$ {{ number_format($bestProduct['revenue'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($topProducts) && $topProducts->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">
                    Top 5 produtos por receita
                </h3>
                <ul class="space-y-1 text-sm">
                    @foreach($topProducts as $product)
                        <li class="flex items-center justify-between">
                            <span class="truncate">
                                {{ $product['name'] }}
                            </span>
                            <span class="ml-3 text-xs text-slate-500">
                                Qtd: <span class="font-medium text-slate-700">{{ $product['quantity'] }}</span> ·
                                Receita: <span class="font-medium text-emerald-600">{{ number_format($product['revenue'], 2, ',', '.') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if(isset($topCities) && $topCities->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm p-4 mb-8">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">
                Top 10 cidades por receita
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-left text-xs uppercase text-slate-500">
                        <th class="py-2 pr-4">Cidade</th>
                        <th class="py-2 pr-4">País</th>
                        <th class="py-2 pr-4">Pedidos</th>
                        <th class="py-2 pr-4">Receita</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topCities as $city)
                        <tr class="border-b last:border-0">
                            <td class="py-2 pr-4">{{ $city['city'] }}</td>
                            <td class="py-2 pr-4 text-slate-500">{{ $city['country'] }}</td>
                            <td class="py-2 pr-4">{{ $city['orders'] }}</td>
                            <td class="py-2 pr-4 text-emerald-600">
                                {{ number_format($city['revenue'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-700">
                    Pedidos (primeiros 20)
                </h3>
                <span class="text-xs text-slate-400">
                    Total: {{ $metrics['total_orders'] }} pedidos
                </span>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 text-xs md:text-sm">
                <label for="status" class="text-slate-600">
                    Status:
                </label>
                <select
                    id="status"
                    name="status"
                    class="border border-slate-300 rounded-md px-2 py-1 text-xs md:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                >
                    @foreach($allowedStatuses as $value => $label)
                        <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="inline-flex items-center px-3 py-1 rounded-md bg-slate-900 text-white text-xs md:text-sm hover:bg-slate-800"
                >
                    Aplicar
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm">
                <thead>
                <tr class="border-b text-left text-xs uppercase text-slate-500">
                    <th class="py-2 pr-3">ID</th>
                    <th class="py-2 pr-3">Número</th>
                    <th class="py-2 pr-3">Data</th>
                    <th class="py-2 pr-3">Cliente</th>
                    <th class="py-2 pr-3">Email</th>
                    <th class="py-2 pr-3">Status</th>
                    <th class="py-2 pr-3">Fulfillment</th>
                    <th class="py-2 pr-3">Valor</th>
                    <th class="py-2 pr-3">Moeda</th>
                    <th class="py-2 pr-3">Cidade</th>
                    <th class="py-2 pr-3">País</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders->take(20) as $order)
                    @php
                        $isRefunded = ($order['status'] ?? '') === 'Refunded';
                    @endphp
                    <tr class="border-b last:border-0 hover:bg-slate-50 {{ $isRefunded ? 'bg-rose-50/40' : '' }}">
                        <td class="py-2 pr-3">{{ $order['id'] }}</td>
                        <td class="py-2 pr-3">{{ $order['order_no'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap">{{ $order['created_at'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap">{{ $order['customer'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap text-slate-500">{{ $order['email'] }}</td>
                        <td class="py-2 pr-3">
                            @php
                                $status = $order['status'] ?? '';
                                $statusLabel = $status;

                                $statusClasses = match ($status) {
                                    'Fulfilled' => 'bg-emerald-100 text-emerald-700',
                                    'Partially Fulfilled' => 'bg-amber-100 text-amber-700',
                                    'Unfulfilled' => 'bg-slate-100 text-slate-700',
                                    'Refunded' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp

                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="py-2 pr-3">
                            @php
                                $fs = $order['fulfillment_status'] ?? '';
                                $fsLabel = $fs ?: '—';

                                $fsClasses = match ($fs) {
                                    'Fully Fulfilled' => 'bg-emerald-50 text-emerald-700',
                                    'Partially Fulfilled' => 'bg-amber-50 text-amber-700',
                                    'Unfulfilled' => 'bg-slate-50 text-slate-700',
                                    default => 'bg-slate-50 text-slate-500',
                                };
                            @endphp

                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $fsClasses }}">
                                {{ $fsLabel }}
                            </span>
                        </td>
                        <td class="py-2 pr-3 text-right">
                            {{ number_format($order['amount'], 2, ',', '.') }}
                        </td>
                        <td class="py-2 pr-3">{{ $order['currency'] }}</td>
                        <td class="py-2 pr-3">{{ $order['city'] }}</td>
                        <td class="py-2 pr-3">{{ $order['country'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(!empty($salesByDay))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            (function () {
                const salesData = @json($salesByDay);

                if (!Array.isArray(salesData) || salesData.length === 0) {
                    return;
                }

                const labels = salesData.map(item => item.date);
                const values = salesData.map(item => item.revenue);

                const ctx = document.getElementById('salesByDayChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Faturamento por dia',
                            data: values,
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Data'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Receita'
                                },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        const value = ctx.parsed.y ?? 0;
                                        return 'Receita: ' + value.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endif
@endsection
