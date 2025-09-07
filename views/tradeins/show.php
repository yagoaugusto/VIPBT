<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> <?php echo $tradeIn->customer_nome; ?><br>
                        <strong>Avaliador:</strong> <?php echo $tradeIn->avaliador_nome; ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong> 
                        <span class="badge 
                            <?php 
                                switch($tradeIn->status){
                                    case 'pendente': echo 'bg-warning'; break;
                                    case 'aprovado': echo 'bg-success'; break;
                                    case 'reprovado': echo 'bg-danger'; break;
                                    case 'creditado': echo 'bg-info'; break;
                                }
                            ?>
                        ">
                            <?php echo ucfirst($tradeIn->status); ?>
                        </span>
                    </div>
                    <div class="col-md-4 text-end">
                        <strong>Data Criação:</strong> <?php echo date('d/m/Y H:i', strtotime($tradeIn->created_at)); ?>
                    </div>
                </div>

                <h5>Itens Avaliados</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Grade</th>
                            <th>Série</th>
                            <th>Valor Avaliado</th>
                            <th>Valor Creditado</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tradeInItems as $item): ?>
                        <tr>
                            <td><?php echo $item->modelo_texto ?: ($item->product_nome ?: 'N/A'); ?></td>
                            <td><?php echo $item->grade ?: 'N/A'; ?></td>
                            <td><?php echo $item->serie ?: 'N/A'; ?></td>
                            <td>R$ <?php echo number_format($item->avaliacao_valor, 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item->valor_creditado, 2, ',', '.'); ?></td>
                            <td><?php echo $item->observacoes; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo URL_ROOT; ?>/tradeins" class="btn btn-secondary">Voltar para a Lista</a>
            </div>
        </div>
    </div>
</div>