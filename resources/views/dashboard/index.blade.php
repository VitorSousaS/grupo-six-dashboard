<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Dashboard – Teste Grupo Six</title>
</head>
<body>
    <h1>Dashboard – Teste Grupo Six</h1>

    <h2>Dump dos pedidos (primeiros 5)</h2>

    <pre>
@json($orders->take(5), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    </pre>
</body>
</html>
