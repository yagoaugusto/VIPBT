<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><?php echo $title; ?></h3>
                <div>
                    <span class="badge fs-6 
                        <?php 
                            switch($tradeIn->status){
                                case 'pendente': echo 'bg-warning text-dark'; break;
                                case 'aprovado': echo 'bg-success'; break;
                                case 'reprovado': echo 'bg-danger'; break;
                                case 'creditado': echo 'bg-info'; break;
                            }
                        ?>
                    ">
                        <?php echo ucfirst($tradeIn->status); ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php \core\Session::flash('success_message'); ?>
                <?php \core\Session::flash('error_message'); ?>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> <?php echo $tradeIn->customer_nome; ?><br>
                        <strong>Avaliador:</strong> <?php echo $tradeIn->avaliador_nome; ?>
                        <?php if($tradeIn->aprovado_por_nome): ?>
                            <br><strong>Aprovado por:</strong> <?php echo $tradeIn->aprovado_por_nome; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong> 
                        <span class="badge 
                            <?php 
                                switch($tradeIn->status){
                                    case 'pendente': echo 'bg-warning text-dark'; break;
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
                        <strong>Data Criação:</strong> <?php echo date('d/m/Y H:i', strtotime($tradeIn->created_at)); ?><br>
                        <strong>Última Atualização:</strong> <?php echo date('d/m/Y H:i', strtotime($tradeIn->updated_at)); ?>
                    </div>
                </div>

                <?php if($tradeIn->observacoes_aprovacao): ?>
                <div class="alert alert-info">
                    <strong>Observações da Aprovação/Reprovação:</strong><br>
                    <?php echo nl2br(htmlspecialchars($tradeIn->observacoes_aprovacao)); ?>
                </div>
                <?php endif; ?>

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
                        <tr class="table-info fw-bold">
                            <td colspan="3">TOTAL</td>
                            <td>R$ <?php echo number_format($totals->total_avaliado, 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($totals->total_creditado, 2, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Seção de Aprovação/Reprovação para Admins -->
                <?php if((\core\Session::get('user_perfil') == 'admin' || \core\Session::get('user_perfil') == 'financeiro') && $tradeIn->status == 'pendente'): ?>
                <div class="mt-4">
                    <h5>Revisão da Avaliação</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="POST" action="<?php echo URL_ROOT; ?>/tradeins/updateStatus/<?php echo $tradeIn->id; ?>">
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações (opcional):</label>
                                    <textarea name="observacoes" id="observacoes" class="form-control" rows="3" placeholder="Adicione comentários sobre a aprovação/reprovação..."></textarea>
                                </div>
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" name="status" value="aprovado" class="btn btn-success">
                                        <i class="fas fa-check"></i> Aprovar
                                    </button>
                                    <button type="submit" name="status" value="reprovado" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Reprovar
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6>Instruções:</h6>
                                <ul class="mb-0">
                                    <li><strong>Aprovar:</strong> Os créditos ficarão disponíveis para uso em pedidos</li>
                                    <li><strong>Reprovar:</strong> A avaliação será rejeitada e não poderá ser usada</li>
                                    <li>Adicione observações para justificar sua decisão</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo URL_ROOT; ?>/tradeins" class="btn btn-secondary">Voltar para a Lista</a>
            </div>
        </div>
    </div>
</div>