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

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total de pedidos</p>
            <p class="mt-2 text-2xl font-semibold">
                {{ $metrics['total_orders'] }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Receita total</p>
            <p class="mt-2 text-2xl font-semibold">
                {{ number_format($metrics['total_revenue'], 2, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Receita líquida</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-600">
                {{ number_format($metrics['net_revenue'], 2, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total reembolsado</p>
            <p class="mt-2 text-2xl font-semibold text-rose-600">
                {{ number_format($metrics['refund_total'], 2, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pedidos entregues</p>
            <p class="mt-2 text-2xl font-semibold">
                {{ $metrics['delivered_orders'] }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Clientes únicos</p>
            <p class="mt-2 text-2xl font-semibold">
                {{ $metrics['unique_customers'] }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm px-4 py-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Taxa de reembolso</p>
            <p class="mt-2 text-2xl font-semibold {{ $metrics['refund_rate'] > 15 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ number_format($metrics['refund_rate'], 2, ',', '.') }}%
            </p>
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
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">
                    Produto mais vendido
                </h3>
                <p class="text-base font-semibold text-slate-900">
                    {{ $bestProduct['name'] }}
                </p>
                <p class="text-sm text-slate-500 mt-1">
                    Quantidade: <span class="font-medium text-slate-800">{{ $bestProduct['quantity'] }}</span><br>
                    Receita: <span class="font-medium text-emerald-600">{{ number_format($bestProduct['revenue'], 2, ',', '.') }}</span>
                </p>
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
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-slate-700">
                Pedidos (primeiros 20)
            </h3>
            <span class="text-xs text-slate-400">
                Total: {{ $metrics['total_orders'] }} pedidos
            </span>
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
                    <tr class="border-b last:border-0 hover:bg-slate-50">
                        <td class="py-2 pr-3">{{ $order['id'] }}</td>
                        <td class="py-2 pr-3">{{ $order['order_no'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap">{{ $order['created_at'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap">{{ $order['customer'] }}</td>
                        <td class="py-2 pr-3 whitespace-nowrap text-slate-500">{{ $order['email'] }}</td>
                        <td class="py-2 pr-3">{{ $order['status'] }}</td>
                        <td class="py-2 pr-3">{{ $order['fulfillment_status'] }}</td>
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
@endsection
