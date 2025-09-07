<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> <?php echo $loan->customer_nome; ?><br>
                        <strong>Vendedor:</strong> <?php echo $loan->vendedor_nome; ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Data Saída:</strong> <?php echo date('d/m/Y', strtotime($loan->data_saida)); ?><br>
                        <strong>Prev. Retorno:</strong> <?php echo date('d/m/Y', strtotime($loan->data_prevista_retorno)); ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <strong>Status:</strong> 
                        <span class="badge 
                            <?php 
                                switch($loan->status){
                                    case 'aberto': echo 'bg-info'; break;
                                    case 'devolvido': echo 'bg-success'; break;
                                    case 'em_atraso': echo 'bg-danger'; break;
                                    case 'convertido_em_venda': echo 'bg-primary'; break;
                                }
                            ?>
                        ">
                            <?php echo str_replace('_', ' ', ucfirst($loan->status)); ?>
                        </span>
                    </div>
                </div>

                <?php if(!empty($loan->observacoes)): ?>
                <div class="mt-4">
                    <strong>Observações:</strong>
                    <p><?php echo nl2br($loan->observacoes); ?></p>
                </div>
                <?php endif; ?>

                <h5>Itens Emprestados</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produto (Série)</th>
                            <th>Estado Saída</th>
                            <th>Estado Retorno</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($loanItems as $item): ?>
                        <tr>
                            <td><?php echo $item->product_nome; ?> (Série: <?php echo $item->serie; ?>)</td>
                            <td><?php echo $item->estado_saida ?: 'N/A'; ?></td>
                            <td><?php echo $item->estado_retorno ?: 'N/A'; ?></td>
                            <td>
                                <?php if($item->estado_retorno === null): ?>
                                    <form action="<?php echo URL_ROOT; ?>/loans/returnItem/<?php echo $loan->id; ?>/<?php echo $item->stock_item_id; ?>" method="post" class="d-inline">
                                        <div class="input-group">
                                            <input type="text" name="estado_retorno" class="form-control form-control-sm" placeholder="Estado de Retorno" required>
                                            <button type="submit" class="btn btn-sm btn-success">Devolver</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    Devolvido
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <!-- Conversão em Venda -->
                        <?php if($loan->status == 'ativo'): ?>
                            <form action="<?php echo URL_ROOT; ?>/loans/convertToSale/<?php echo $loan->id; ?>" method="POST" class="d-inline-block me-3" onsubmit="return confirm('Tem certeza que deseja converter este empréstimo em venda?')">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <select name="seller_id" class="form-select form-select-sm" required>
                                            <option value="">Selecione o Vendedor</option>
                                            <?php foreach($sellers as $seller): ?>
                                                <option value="<?php echo $seller->id; ?>"><?php echo $seller->nome; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="channel_id" class="form-select form-select-sm" required>
                                            <option value="">Selecione o Canal</option>
                                            <?php foreach($channels as $channel): ?>
                                                <option value="<?php echo $channel->id; ?>"><?php echo $channel->nome; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-shopping-cart"></i> Converter em Venda
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php elseif($loan->status == 'convertido_em_venda'): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Este empréstimo foi convertido em venda.
                                <?php if($loan->order_id): ?>
                                    <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $loan->order_id; ?>" class="btn btn-sm btn-outline-primary ms-2">Ver Pedido</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="<?php echo URL_ROOT; ?>/loans" class="btn btn-secondary">Voltar para a Lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>