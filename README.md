# 📊 Dashboard de Análise de Pedidos - Grupo Six

Este projeto foi desenvolvido como parte do teste técnico para a vaga de **Programador Backend (PHP/Laravel)** no **Grupo Six**.

O objetivo é consumir dados de pedidos a partir de uma API externa, processá‑los por meio de regras de negócio e apresentar um dashboard completo com métricas operacionais, financeiras e analíticas.

---

## 🚀 Tecnologias Utilizadas

- **PHP 8.5**
- **Laravel 12**
- **TailwindCSS** (via CDN)
- **Chart.js** (para gráficos)
- **Laravel Http Client** (para consumo da API)
- **Collections / Lazy Collections**
- **Paginador nativo do Laravel**

---

## 📦 Instalação do Projeto

### 1️⃣ Clone o repositório
```bash
git clone https://github.com/VitorSousaS/grupo-six-dashboard.git
```

### 2️⃣ Instale as dependências
```bash
composer install
```

### 3️⃣ Configure o `.env`
Crie o arquivo:
```bash
cp .env.example .env
```
Edite a variável do teste:
```
TEST_ORDERS_URL=https://dev-crm.ogruposix.com/candidato-teste-pratico-backend-dashboard/test-orders
```

### 4️⃣ Gere a key do Laravel
```bash
php artisan key:generate
```

### 5️⃣ Rode o servidor local
```bash
php artisan serve
```

---

## 🧠 Arquitetura e Organização

### **Services**
- `OrderService.php`  
  Responsável por acessar a API com cache e normalizar o retorno.

- `OrderMetrics.php`  
  Regras de negócio, cálculos e métricas.

---

### **Controller**
- `DashboardController.php`  
  Orquestra filtros, paginação, busca, métricas e dados formatados para a view.

---

### **View**
- `resources/views/dashboard/index.blade.php`  
  Contém o dashboard completo:
  - Cards de métricas
  - Tabela com paginação + busca + filtros
  - Gráficos
  - Listas ranqueadas

---

## 📊 Métricas Implementadas

### 🟩 **Básicas**
✔ Total de pedidos  
✔ Receita total 
✔ Pedidos entregues + taxa de entrega  
✔ Clientes únicos + média por cliente  
✔ Resumo Financeiro  
✔ Taxa de Reembolso  
✔ Produto mais vendido  
✔ Tabela com filtro, busca e paginação  

---

### 🟧 **Intermediárias**
✔ Top 5 produtos por receita  
✔ Top 10 cidades por faturamento  
✔ Pedidos Entregues Depois Reembolsados

---

### 🟥 **Avançada**
✔ Análise temporal de vendas (gráfico da linha do tempo)
✔ Análise de motivos de reembolso (ranqueada)

---

## 🙏 Agradecimento

Agradeço pela oportunidade de participar deste processo seletivo. 
Este projeto reflete não apenas minhas habilidades técnicas, mas também meu cuidado com organização, clareza e qualidade de código.