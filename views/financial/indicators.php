<div class="row mb-3">
    <div class="col-md-8">
        <h1><i class="fas fa-chart-bar me-2"></i><?php echo $title; ?></h1>
        <p class="text-muted">Acompanhe os principais indicadores financeiros e de negócio do seu sistema</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?php echo URL_ROOT; ?>/financial/receivables" class="btn btn-outline-primary me-2">
            <i class="fas fa-money-bill-wave"></i> Contas a Receber
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Período</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo URL_ROOT; ?>/financial/indicators" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $filters['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $filters['end_date'] ?? ''; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="<?php echo URL_ROOT; ?>/financial/indicators" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total de Pedidos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($overview->total_orders ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Vendas Confirmadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($overview->total_sales ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Valor Recebido</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?php echo number_format($overview->amount_received ?? 0, 2, ',', '.'); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Valor a Receber</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?php echo number_format($overview->amount_to_receive ?? 0, 2, ',', '.'); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark">Receita por Mês</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark">Métodos de Pagamento</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <!-- Produtos Mais Vendidos -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-trophy me-2"></i>Produtos Mais Vendidos
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($most_sold_products)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Posição</th>
                                    <th>Produto</th>
                                    <th>Qtd Vendida</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($most_sold_products as $index => $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index == 0): ?>
                                                <i class="fas fa-medal text-warning"></i>
                                            <?php elseif ($index == 1): ?>
                                                <i class="fas fa-medal text-secondary"></i>
                                            <?php elseif ($index == 2): ?>
                                                <i class="fas fa-medal text-dark"></i>
                                            <?php else: ?>
                                                <?php echo $index + 1; ?>°
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product->product_name); ?></strong><br>
                                            <small class="text-muted">SKU: <?php echo htmlspecialchars($product->sku); ?></small>
                                        </td>
                                        <td><span class="badge bg-primary"><?php echo number_format($product->total_quantity); ?></span></td>
                                        <td class="text-success">R$ <?php echo number_format($product->total_value, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum produto vendido no período selecionado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Clientes por Compras -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-user-tie me-2"></i>Top Clientes por Compras
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($top_customers_by_purchases)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Pedidos</th>
                                    <th>Total Gasto</th>
                                    <th>Ticket Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers_by_purchases as $customer): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($customer->customer_name); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($customer->telefone); ?></small>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo number_format($customer->total_orders); ?></span></td>
                                        <td class="text-success">R$ <?php echo number_format($customer->total_spent, 2, ',', '.'); ?></td>
                                        <td class="text-secondary">R$ <?php echo number_format($customer->avg_order_value, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma compra no período selecionado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Clientes por Empréstimos -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-handshake me-2"></i>Top Clientes por Empréstimos
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($top_customers_by_loans)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Empréstimos</th>
                                    <th>Itens</th>
                                    <th>Convertidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers_by_loans as $customer): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($customer->customer_name); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($customer->telefone); ?></small>
                                        </td>
                                        <td><span class="badge bg-warning"><?php echo number_format($customer->total_loans); ?></span></td>
                                        <td><span class="badge bg-secondary"><?php echo number_format($customer->total_items_borrowed); ?></span></td>
                                        <td><span class="badge bg-success"><?php echo number_format($customer->loans_converted_to_sales); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum empréstimo no período selecionado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Canal -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-chart-line me-2"></i>Performance por Canal de Venda
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($sales_channel_stats)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Canal</th>
                                    <th>Pedidos</th>
                                    <th>Vendas</th>
                                    <th>Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales_channel_stats as $channel): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($channel->channel_name); ?></strong></td>
                                        <td><span class="badge bg-primary"><?php echo number_format($channel->total_orders); ?></span></td>
                                        <td><span class="badge bg-success"><?php echo number_format($channel->total_sales); ?></span></td>
                                        <td class="text-success">R$ <?php echo number_format($channel->total_revenue, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma venda por canal no período selecionado</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-xs {
    font-size: 0.7rem;
}
.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
/* Melhorias de contraste */
.card-header {
    background-color: #f8f9fc !important;
}
.card-header h6,
.card .text-xs,
.card .h5 {
    color: #212529 !important;
}
.table thead th {
    background-color: #f8f9fc;
    color: #212529;
}
</style>

<script>
// Monthly Revenue Chart
const monthlyRevenueData = <?php echo json_encode(array_reverse($monthly_revenue_data)); ?>;
const monthLabels = monthlyRevenueData.map(item => {
    const [year, month] = item.month.split('-');
    const monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    return `${monthNames[parseInt(month) - 1]}/${year}`;
});
const revenueValues = monthlyRevenueData.map(item => parseFloat(item.revenue));

const ctx1 = document.getElementById('monthlyRevenueChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Receita (R$)',
            data: revenueValues,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#495057',
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                ticks: { color: '#495057' },
                grid: { color: 'rgba(0,0,0,0.03)' }
            }
        },
        plugins: {
            legend: {
                display: false,
                labels: { color: '#495057' }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Receita: R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                },
                titleColor: '#212529',
                bodyColor: '#212529'
            }
        }
    }
});

// Payment Method Chart
const paymentMethodData = <?php echo json_encode($payment_method_stats); ?>;
const paymentLabels = paymentMethodData.map(item => {
    const methodNames = {
        'pix': 'PIX',
        'cartao': 'Cartão',
        'dinheiro': 'Dinheiro',
        'boleto': 'Boleto',
        'transferencia': 'Transferência',
        'outros': 'Outros'
    };
    return methodNames[item.payment_method] || item.payment_method;
});
const paymentValues = paymentMethodData.map(item => parseFloat(item.total_amount));

const ctx2 = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: paymentLabels,
        datasets: [{
            data: paymentValues,
            backgroundColor: [
                '#4e73df',
                '#1cc88a', 
                '#36b9cc',
                '#f6c23e',
                '#e74a3b',
                '#858796'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 15,
                    color: '#495057'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                    }
                },
                titleColor: '#212529',
                bodyColor: '#212529'
            }
        }
    }
});
</script>