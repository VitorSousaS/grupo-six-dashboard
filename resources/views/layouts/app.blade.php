<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard – Teste Grupo Six')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">

    <header class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <h1 class="text-lg font-semibold">
                Grupo Six – Dashboard de Pedidos
            </h1>
            <span class="text-xs md:text-sm text-slate-300">
                Teste técnico – Laravel + PHP
            </span>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="max-w-6xl mx-auto px-4 pb-6 text-xs text-slate-500">
        Dashboard gerado a partir de API externa de pedidos (dados fictícios).
    </footer>
</body>
</html>
