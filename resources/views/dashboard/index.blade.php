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


    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                Total de pedidos
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $metrics['total_orders'] }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Contagem total de pedidos recebidos
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
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-700">
                        Top 5 produtos por receita
                    </h3>
                    <span class="text-[11px] uppercase tracking-wide text-slate-400">
                        Ranqueado por receita (R$)
                    </span>
                </div>

                @php
                    $maxRevenue = $topProducts->max('revenue') ?: 1;
                @endphp

                <ul class="space-y-2 text-xs md:text-sm">
                    @foreach($topProducts as $index => $product)
                        <li class="flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-900 text-white text-xs font-semibold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="truncate font-medium text-slate-800">
                                        {{ $product['name'] }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div>
                                        Qtd: <span class="font-semibold text-slate-800">{{ $product['quantity'] }}</span>
                                    </div>
                                    <div>
                                        Receita: <span class="font-semibold text-emerald-700">
                                            R$ {{ number_format($product['revenue'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if(!empty($salesByDay))
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">
                Faturamento por dia
            </h3>
            <p class="text-xs text-slate-500 mb-3">
                Evolução diária do faturamento (em R$), com base na data de criação dos pedidos.
            </p>
            <div class="h-64">
                <canvas id="salesByDayChart"></canvas>
            </div>
        </div>
    @endif

    @if(isset($topCities) && $topCities->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm p-4 mb-8">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                Top 10 cidades por receita
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-xs uppercase text-slate-500">
                        <th class="py-2 pr-4">Rank</th>
                        <th class="py-2 pr-4">Cidade</th>
                        <th class="py-2 pr-4">País</th>
                        <th class="py-2 pr-4 text-center">Pedidos</th>
                        <th class="py-2 pr-4 text-right">Receita</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($topCities as $index => $city)
                        @php
                            $rank = $index + 1;

                            $badgeColor = match($rank) {
                                1 => 'bg-amber-300 text-amber-900',
                                2 => 'bg-slate-300 text-slate-800',
                                3 => 'bg-amber-600 text-white',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <tr class="border-b last:border-0 hover:bg-slate-50">
                            <td class="py-2 pr-4 text-center">
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $badgeColor }}">
                                    #{{ $rank }}
                                </span>
                            </td>

                            <td class="py-2 pr-4 font-medium text-slate-800 text-center">
                                {{ $city['city'] }}
                            </td>

                            <td class="py-2 pr-4 text-slate-600 text-center">
                                {{ $city['country'] }}
                            </td>

                            <td class="py-2 pr-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-900 text-white text-xs font-semibold">
                                    {{ $city['orders'] }}
                                </span>
                            </td>

                            <td class="py-2 pr-4 text-right font-semibold text-emerald-600">
                                R$ {{ number_format($city['revenue'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2 mb-8">
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

        @if(
            isset($metrics['delivered_refunded_count']) &&
            isset($metrics['delivered_refunded_total']) &&
            $metrics['delivered_refunded_total'] > 0
        )
            @php
                $count = $metrics['delivered_refunded_count'];
                $totalDelivered = $metrics['delivered_refunded_total'];
                $rate = $metrics['delivered_refunded_rate_formatted'] ?? (
                    number_format($metrics['delivered_refunded_rate'] ?? 0, 2, ',', '.') . '%'
                );

                // Define a "gravidade" visual
                if ($metrics['delivered_refunded_rate'] >= 10) {
                    $bg = 'bg-rose-50';
                    $border = 'border-rose-200';
                    $badge = 'bg-rose-100 text-rose-700';
                } elseif ($metrics['delivered_refunded_rate'] >= 5) {
                    $bg = 'bg-amber-50';
                    $border = 'border-amber-200';
                    $badge = 'bg-amber-100 text-amber-700';
                } else {
                    $bg = 'bg-slate-50';
                    $border = 'border-slate-200';
                    $badge = 'bg-slate-100 text-slate-700';
                }
            @endphp

            <div class="{{ $bg }} {{ $border }} border rounded-xl px-4 py-3 flex flex-col gap-1 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">
                        Entregues depois reembolsados
                    </p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $badge }}">
                        {{ $rate }}
                    </span>
                </div>

                <p class="text-sm text-slate-800 mt-1">
                    {{ $count }} de {{ $totalDelivered }} pedidos entregues foram reembolsados.
                </p>

                <p class="text-[11px] text-slate-500 mt-1">
                    Pedidos <span class="font-medium">"Fully Fulfilled"</span> que possuem reembolsos.
                </p>
            </div>
        @endif
    </div>

    @if(isset($refundReasons) && $refundReasons->isNotEmpty())
        @php
            $totalReasons = $refundReasons->sum('count');
        @endphp

        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-rose-100">
            <h3 class="text-sm font-semibold text-rose-700 mb-2">
                Motivos de reembolso
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-xs uppercase text-slate-500">
                        <th class="py-2 pr-3 text-left">Motivo</th>
                        <th class="py-2 pr-3 text-center">Ocorrências</th>
                        <th class="py-2 pr-3 text-right">Percentual</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($refundReasons as $row)
                        @php
                            $count = $row['count'];
                            $percent = $totalReasons > 0
                                ? ($count / $totalReasons) * 100
                                : 0;
                        @endphp

                        <tr class="border-b last:border-0 hover:bg-rose-50/40">
                            <td class="py-2 pr-3">
                                <span class="text-slate-800">
                                    {{ $row['reason'] }}
                                </span>
                            </td>
                            <td class="py-2 pr-3 text-center font-medium">
                                {{ $count }}
                            </td>
                            <td class="py-2 pr-3 text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold">
                                    {{ number_format($percent, 2, ',', '.') }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div id="orders-table" class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-700">
                    Pedidos
                </h3>
                <span class="text-xs text-slate-400">
                    Total: {{ $metrics['total_orders'] }} pedidos
                </span>
            </div>

            <form method="GET" 
                  action="{{ route('dashboard') }}#orders-table" 
                  class="flex flex-col md:flex-row md:items-center gap-2 text-xs md:text-sm">
                <div class="flex items-center gap-2">
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
                </div>

                <div class="flex items-center gap-2">
                    <label for="search" class="text-slate-600">
                        Buscar:
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="ID, número, cliente, email..."
                        class="border border-slate-300 rounded-md px-2 py-1 text-xs md:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-slate-600">
                        Por página:
                    </label>
                    <select
                        id="per_page"
                        name="per_page"
                        class="border border-slate-300 rounded-md px-2 py-1 text-xs md:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                    >
                        @foreach([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ ($perPage ?? 20) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-3 py-1 rounded-md bg-slate-900 text-white text-xs md:text-sm hover:bg-slate-800"
                    >
                        Aplicar
                    </button>

                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-1 rounded-md border border-slate-300 text-slate-600 text-xs md:text-sm hover:bg-slate-50"
                    >
                        Limpar
                    </a>
                </div>
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
                @foreach($orders as $order)
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

        <div class="mt-3">
            {{ $orders->links() }}
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
                                        return 'Receita: R$ ' + value
                                        .toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        });
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
