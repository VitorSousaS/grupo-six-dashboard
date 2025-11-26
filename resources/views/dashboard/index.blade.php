<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Dashboard – Teste Grupo Six</title>
</head>
<body>
    <h1>Dashboard – Teste Grupo Six</h1>

    <h2>Métricas básicas</h2>

    <ul>
        <li>Total de pedidos: {{ $metrics['total_orders'] }}</li>
        <li>Receita total (local): {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</li>
        <li>Receita líquida: {{ number_format($metrics['net_revenue'], 2, ',', '.') }}</li>
        <li>Total reembolsado: {{ number_format($metrics['refund_total'], 2, ',', '.') }}</li>
        <li>Pedidos entregues: {{ $metrics['delivered_orders'] }}</li>
        <li>Clientes únicos: {{ $metrics['unique_customers'] }}</li>
        <li>Taxa de reembolso: {{ number_format($metrics['refund_rate'], 2, ',', '.') }}%</li>
    </ul>

    @if($bestProduct)
        <h2>Produto mais vendido</h2>
        <p>
            {{ $bestProduct['name'] }}<br>
            Quantidade: {{ $bestProduct['quantity'] }}<br>
            Receita: {{ number_format($bestProduct['revenue'], 2, ',', '.') }}
        </p>
    @endif

    <h2>Pedidos (primeiros 20)</h2>
    <table border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Status</th>
                <th>Fulfillment</th>
                <th>Valor</th>
                <th>Moeda</th>
                <th>Cidade</th>
                <th>País</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders->take(20) as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['order_no'] }}</td>
                    <td>{{ $order['created_at'] }}</td>
                    <td>{{ $order['customer'] }}</td>
                    <td>{{ $order['email'] }}</td>
                    <td>{{ $order['status'] }}</td>
                    <td>{{ $order['fulfillment_status'] }}</td>
                    <td>{{ number_format($order['amount'], 2, ',', '.') }}</td>
                    <td>{{ $order['currency'] }}</td>
                    <td>{{ $order['city'] }}</td>
                    <td>{{ $order['country'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
