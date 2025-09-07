<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> <?php echo $order->customer_nome; ?><br>
                        <strong>Telefone:</strong> <?php echo $order->customer_telefone; ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Vendedor:</strong> <?php echo $order->seller_nome; ?><br>
                        <strong>Canal:</strong> <?php echo $order->channel_nome; ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <strong>Data do Pedido:</strong> <?php echo date('d/m/Y', strtotime($order->data)); ?><br>
                        <strong>Código Público:</strong> <span class="badge bg-info"><?php echo $order->public_code; ?></span>
                    </div>
                </div>

                <!-- Status do Pedido -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title">Status do Pedido</h6>
                                <span class="badge <?php 
                                    switch($order->status_pedido){
                                        case 'novo': echo 'bg-primary'; break;
                                        case 'faturado': echo 'bg-success'; break;
                                        case 'cancelado': echo 'bg-danger'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>"><?php echo ucfirst($order->status_pedido); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title">Status Fiscal</h6>
                                <span class="badge <?php 
                                    switch($order->status_fiscal){
                                        case 'nao_faturado': echo 'bg-warning'; break;
                                        case 'faturado': echo 'bg-success'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>"><?php echo str_replace('_', ' ', ucfirst($order->status_fiscal)); ?></span>
                                <?php if($order->status_fiscal == 'nao_faturado'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="updateFiscalStatus('<?php echo $order->id; ?>', 'faturado')">
                                        Faturar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title">Status de Entrega</h6>
                                <span class="badge <?php 
                                    switch($order->status_entrega){
                                        case 'nao_entregue': echo 'bg-secondary'; break;
                                        case 'preparando': echo 'bg-warning'; break;
                                        case 'enviado': echo 'bg-info'; break;
                                        case 'entregue': echo 'bg-success'; break;
                                        case 'entrega_parcial': echo 'bg-primary'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>"><?php echo str_replace('_', ' ', ucfirst($order->status_entrega)); ?></span>
                                <?php if($order->status_entrega != 'entregue'): ?>
                                    <div class="dropdown d-inline-block ms-2">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Atualizar
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="updateDeliveryStatus('<?php echo $order->id; ?>', 'preparando')">Preparando</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateDeliveryStatus('<?php echo $order->id; ?>', 'enviado')">Enviado</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateDeliveryStatus('<?php echo $order->id; ?>', 'entregue')">Entregue</a></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h5>Itens do Pedido</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th class="text-end">Qtd.</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Desconto</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach($items as $item): 
                            $subtotal = ($item->preco_unit * $item->qtd) - $item->desconto;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $item->sku; ?></td>
                            <td><?php echo $item->product_nome; ?></td>
                            <td class="text-end"><?php echo $item->qtd; ?></td>
                            <td class="text-end">R$ <?php echo number_format($item->preco_unit, 2, ',', '.'); ?></td>
                            <td class="text-end">R$ <?php echo number_format($item->desconto, 2, ',', '.'); ?></td>
                            <td class="text-end">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Subtotal:</th>
                            <th class="text-end">R$ <?php echo number_format($total, 2, ',', '.'); ?></th>
                        </tr>
                        <?php if(!empty($credits)): 
                            $totalCredits = array_sum(array_column($credits, 'valor'));
                        ?>
                        <tr class="text-success">
                            <th colspan="5" class="text-end">Créditos:</th>
                            <th class="text-end">- R$ <?php echo number_format($totalCredits, 2, ',', '.'); ?></th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="5" class="text-end">Total Final:</th>
                            <th class="text-end">R$ <?php echo number_format(max(0, $total - $totalCredits), 2, ',', '.'); ?></th>
                        </tr>
                        <?php else: ?>
                        <tr class="table-primary">
                            <th colspan="5" class="text-end">Total Final:</th>
                            <th class="text-end">R$ <?php echo number_format($total, 2, ',', '.'); ?></th>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>

                <!-- Fulfillment / Expedição -->
                <?php if(!empty($fulfillments)): ?>
                <h5 class="mt-4">Informações de Expedição</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Transportadora</th>
                                <th>Código Rastreio</th>
                                <th>Enviado em</th>
                                <th>Entregue em</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($fulfillments as $fulfillment): ?>
                            <tr>
                                <td>
                                    <span class="badge <?php 
                                        switch($fulfillment->status){
                                            case 'preparando': echo 'bg-warning'; break;
                                            case 'enviado': echo 'bg-info'; break;
                                            case 'entregue': echo 'bg-success'; break;
                                            default: echo 'bg-secondary';
                                        }
                                    ?>"><?php echo ucfirst($fulfillment->status); ?></span>
                                </td>
                                <td><?php echo $fulfillment->transportadora ?: '-'; ?></td>
                                <td><?php echo $fulfillment->codigo_rastreio ?: '-'; ?></td>
                                <td><?php echo $fulfillment->enviado_em ? date('d/m/Y H:i', strtotime($fulfillment->enviado_em)) : '-'; ?></td>
                                <td><?php echo $fulfillment->entregue_em ? date('d/m/Y H:i', strtotime($fulfillment->entregue_em)) : '-'; ?></td>
                                <td><?php echo $fulfillment->observacoes ?: '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Adicionar/Gerenciar Fulfillment -->
                <?php if($order->status_pedido != 'cancelado' && $order->status_entrega != 'entregue'): ?>
                <div class="mt-4">
                    <h6>Gerenciar Expedição</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fulfillmentModal">
                        <i class="fas fa-truck"></i> Adicionar Fulfillment
                    </button>
                </div>
                <?php endif; ?>

                <!-- Créditos Aplicados -->
                <?php if(!empty($credits)): ?>
                <h5 class="mt-4">Créditos Aplicados</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Origem</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCredits = 0;
                            foreach($credits as $credit): 
                                $totalCredits += $credit->valor;
                            ?>
                            <tr>
                                <td><span class="badge bg-info"><?php echo ucfirst($credit->origem); ?></span></td>
                                <td><?php echo $credit->descricao; ?></td>
                                <td class="text-end text-success">- R$ <?php echo number_format($credit->valor, 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($credit->created_at)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total Créditos:</th>
                                <th class="text-end text-success">- R$ <?php echo number_format($totalCredits, 2, ',', '.'); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>

                <?php if(!empty($order->observacao)): ?>
                <div class="mt-4">
                    <strong>Observações:</strong>
                    <p><?php echo nl2br($order->observacao); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="<?php echo URL_ROOT; ?>/publicorders/consulta/<?php echo $order->public_code; ?>" class="btn btn-outline-info" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Visualização Pública
                        </a>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?php echo URL_ROOT; ?>/orders" class="btn btn-secondary">Voltar para a Lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Fulfillment -->
<div class="modal fade" id="fulfillmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Fulfillment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URL_ROOT; ?>/logistics/addFulfillment" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="preparando">Preparando</option>
                            <option value="enviado">Enviado</option>
                            <option value="entregue">Entregue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="transportadora" class="form-label">Transportadora</label>
                        <input type="text" name="transportadora" id="transportadora" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="codigo_rastreio" class="form-label">Código de Rastreio</label>
                        <input type="text" name="codigo_rastreio" id="codigo_rastreio" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Fulfillment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateFiscalStatus(orderId, status) {
    if(confirm('Tem certeza que deseja atualizar o status fiscal?')) {
        fetch(`<?php echo URL_ROOT; ?>/orders/updateFiscalStatus/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro de comunicação');
        });
    }
}

function updateDeliveryStatus(orderId, status) {
    if(confirm('Tem certeza que deseja atualizar o status de entrega?')) {
        fetch(`<?php echo URL_ROOT; ?>/orders/updateDeliveryStatus/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro de comunicação');
        });
    }
}
</script>