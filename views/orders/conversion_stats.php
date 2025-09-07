<div class="row mb-3">
    <div class="col-md-8">
        <h1><?php echo $title; ?></h1>
        <p class="text-muted">Acompanhe a conversão de pedidos em vendas por período, canal e vendedor</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?php echo URL_ROOT; ?>/orders" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar para Pedidos
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo URL_ROOT; ?>/orders/conversionStats">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Data Início</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="<?php echo $filters['start_date'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Data Fim</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="<?php echo $filters['end_date'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="channel_id" class="form-label">Canal de Venda</label>
                    <select name="channel_id" id="channel_id" class="form-select">
                        <option value="">Todos os canais</option>
                        <?php if(isset($channels)): ?>
                            <?php foreach($channels as $channel): ?>
                                <option value="<?php echo $channel->id; ?>" 
                                    <?php echo ($filters['channel_id'] == $channel->id) ? 'selected' : ''; ?>>
                                    <?php echo $channel->nome; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="seller_id" class="form-label">Vendedor</label>
                    <select name="seller_id" id="seller_id" class="form-select">
                        <option value="">Todos os vendedores</option>
                        <?php if(isset($sellers)): ?>
                            <?php foreach($sellers as $seller): ?>
                                <option value="<?php echo $seller->id; ?>" 
                                    <?php echo ($filters['seller_id'] == $seller->id) ? 'selected' : ''; ?>>
                                    <?php echo $seller->nome ?? $seller->user_nome ?? 'Vendedor #' . $seller->id; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo URL_ROOT; ?>/orders/conversionStats" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Gerais -->
<?php if(isset($overall_stats)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resumo Geral</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h4 class="text-primary"><?php echo $overall_stats->total_pedidos; ?></h4>
                        <p class="mb-0">Total de Pedidos</p>
                    </div>
                    <div class="col-md-2">
                        <h4 class="text-success"><?php echo $overall_stats->vendas_confirmadas; ?></h4>
                        <p class="mb-0">Vendas Confirmadas</p>
                    </div>
                    <div class="col-md-2">
                        <h4 class="text-warning"><?php echo $overall_stats->pedidos_pendentes; ?></h4>
                        <p class="mb-0">Pedidos Pendentes</p>
                    </div>
                    <div class="col-md-2">
                        <h4 class="text-danger"><?php echo $overall_stats->pedidos_cancelados; ?></h4>
                        <p class="mb-0">Pedidos Cancelados</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-info"><?php echo $overall_stats->taxa_conversao_percent; ?>%</h4>
                        <p class="mb-0">Taxa de Conversão</p>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $overall_stats->taxa_conversao_percent; ?>%"
                                 aria-valuenow="<?php echo $overall_stats->taxa_conversao_percent; ?>" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Estatísticas por Canal -->
<?php if(isset($stats_by_channel) && !empty($stats_by_channel)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Performance por Canal de Venda</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Canal</th>
                                <th class="text-center">Total Pedidos</th>
                                <th class="text-center">Vendas Confirmadas</th>
                                <th class="text-center">Pendentes</th>
                                <th class="text-center">Cancelados</th>
                                <th class="text-center">Taxa Conversão</th>
                                <th class="text-end">Valor Total Vendas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats_by_channel as $stat): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $stat->canal_nome ?: 'Canal não encontrado'; ?></strong>
                                </td>
                                <td class="text-center"><?php echo $stat->total_pedidos; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo $stat->vendas_confirmadas; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning"><?php echo $stat->pedidos_pendentes; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?php echo $stat->pedidos_cancelados; ?></span>
                                </td>
                                <td class="text-center">
                                    <strong><?php echo $stat->taxa_conversao_percent; ?>%</strong>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?php echo $stat->taxa_conversao_percent; ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong>R$ <?php echo number_format($stat->valor_total_vendas, 2, ',', '.'); ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Estatísticas por Vendedor -->
<?php if(isset($stats_by_seller) && !empty($stats_by_seller)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Performance por Vendedor</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th class="text-center">Total Pedidos</th>
                                <th class="text-center">Vendas Confirmadas</th>
                                <th class="text-center">Pendentes</th>
                                <th class="text-center">Cancelados</th>
                                <th class="text-center">Taxa Conversão</th>
                                <th class="text-end">Valor Total Vendas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats_by_seller as $stat): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $stat->vendedor_nome ?: 'Vendedor não encontrado'; ?></strong>
                                </td>
                                <td class="text-center"><?php echo $stat->total_pedidos; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo $stat->vendas_confirmadas; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning"><?php echo $stat->pedidos_pendentes; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?php echo $stat->pedidos_cancelados; ?></span>
                                </td>
                                <td class="text-center">
                                    <strong><?php echo $stat->taxa_conversao_percent; ?>%</strong>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?php echo $stat->taxa_conversao_percent; ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong>R$ <?php echo number_format($stat->valor_total_vendas, 2, ',', '.'); ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!isset($overall_stats) || $overall_stats->total_pedidos == 0): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5>Nenhum dado encontrado</h5>
                <p class="text-muted">Não há pedidos no período selecionado ou com os filtros aplicados.</p>
                <a href="<?php echo URL_ROOT; ?>/orders/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Pedido
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>