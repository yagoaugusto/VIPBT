<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
        <p class="text-muted">Saldos calculados com base nas movimentações de estoque</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/stock" class="btn btn-secondary me-2">
            <i class="fas fa-list"></i> Itens Físicos
        </a>
        <a href="<?php echo URL_ROOT; ?>/stock/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Entrada
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto (SKU)</th>
                    <th>Marca</th>
                    <th>Tipo</th>
                    <th class="text-center">Saldo Calculado</th>
                    <th class="text-center">Em Estoque</th>
                    <th class="text-center">Reservados</th>
                    <th class="text-center">Emprestados</th>
                    <th class="text-center">Vendidos</th>
                    <th class="text-center">Total Físico</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($stockBalances)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">Nenhum produto com estoque encontrado</td>
                </tr>
                <?php else: ?>
                    <?php foreach($stockBalances as $balance): ?>
                    <tr>
                        <td>
                            <strong><?php echo $balance->product_nome; ?></strong><br>
                            <small class="text-muted"><?php echo $balance->sku; ?></small>
                        </td>
                        <td><?php echo $balance->brand_nome; ?></td>
                        <td>
                            <span class="badge <?php echo $balance->tipo_condicao == 'novo' ? 'bg-success' : 'bg-info'; ?>">
                                <?php echo ucfirst($balance->tipo_condicao); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $balance->saldo_calculado > 0 ? 'bg-success' : ($balance->saldo_calculado < 0 ? 'bg-danger' : 'bg-secondary'); ?> fs-6">
                                <?php echo $balance->saldo_calculado; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">
                                <?php echo $balance->itens_disponiveis ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning">
                                <?php echo $balance->itens_reservados ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                <?php echo $balance->itens_emprestados ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                <?php echo $balance->itens_vendidos ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <strong><?php echo $balance->itens_fisicos ?: 0; ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Legenda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Saldo Calculado:</strong> Quantidade baseada nas movimentações (Entradas - Saídas)</p>
                        <p><strong>Em Estoque:</strong> Itens físicos disponíveis para venda</p>
                        <p><strong>Reservados:</strong> Itens físicos reservados para vendas específicas</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Emprestados:</strong> Itens físicos em empréstimo de teste</p>
                        <p><strong>Vendidos:</strong> Itens físicos já vendidos</p>
                        <p><strong>Total Físico:</strong> Soma de todos os itens físicos cadastrados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>