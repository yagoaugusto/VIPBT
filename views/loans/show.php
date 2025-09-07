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
            <div class="card-footer text-end">
                <a href="<?php echo URL_ROOT; ?>/loans" class="btn btn-secondary">Voltar para a Lista</a>
            </div>
        </div>
    </div>
</div>