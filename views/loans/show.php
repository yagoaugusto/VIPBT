<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1><?php echo $title; ?></h1>
            <a href="<?php echo URL_ROOT; ?>/loans" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para Lista
            </a>
        </div>
        
        <?php core\Session::flash('loan_message'); ?>
        
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Empréstimo #<?php echo $loan->id; ?></h3>
                    </div>
                    <div class="col-auto">
                        <span class="badge fs-6
                            <?php 
                                switch($loan->status){
                                    case 'ativo': echo 'bg-warning text-dark'; break;
                                    case 'devolvido': echo 'bg-success'; break;
                                    case 'em_atraso': echo 'bg-danger'; break;
                                    case 'convertido_em_venda': echo 'bg-primary'; break;
                                    default: echo 'bg-secondary'; break;
                                }
                            ?>
                        ">
                            <?php 
                                switch($loan->status){
                                    case 'ativo': echo 'Ativo'; break;
                                    case 'devolvido': echo 'Devolvido'; break;
                                    case 'em_atraso': echo 'Em Atraso'; break;
                                    case 'convertido_em_venda': echo 'Convertido em Venda'; break;
                                    default: echo ucfirst($loan->status); break;
                                }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user"></i> Informações do Cliente</h6>
                        <p class="mb-1"><strong>Nome:</strong> <?php echo htmlspecialchars($loan->customer_nome); ?></p>
                        <p class="mb-0"><strong>Vendedor:</strong> <?php echo htmlspecialchars($loan->vendedor_nome); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar"></i> Datas</h6>
                        <p class="mb-1">
                            <strong>Data Saída:</strong> 
                            <span class="badge bg-light text-dark"><?php echo date('d/m/Y', strtotime($loan->data_saida)); ?></span>
                        </p>
                        <p class="mb-0">
                            <strong>Prev. Retorno:</strong> 
                            <?php if($loan->data_prevista_retorno): ?>
                                <?php 
                                    $is_late = $loan->status === 'ativo' && strtotime($loan->data_prevista_retorno) < strtotime('today');
                                ?>
                                <span class="badge <?php echo $is_late ? 'bg-danger' : 'bg-light text-dark'; ?>">
                                    <?php echo date('d/m/Y', strtotime($loan->data_prevista_retorno)); ?>
                                    <?php if($is_late): ?>
                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Não definida</span>
                            <?php endif; ?>
                        </p>
                        <?php if($loan->data_retorno): ?>
                        <p class="mb-0">
                            <strong>Data Retorno:</strong> 
                            <span class="badge bg-success"><?php echo date('d/m/Y', strtotime($loan->data_retorno)); ?></span>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!empty($loan->observacoes)): ?>
                <div class="alert alert-info">
                    <h6><i class="fas fa-sticky-note"></i> Observações</h6>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($loan->observacoes)); ?></p>
                </div>
                <?php endif; ?>

                <h5 class="mt-4 mb-3"><i class="fas fa-box"></i> Itens Emprestados</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produto (Série)</th>
                                <th>Estado Saída</th>
                                <th>Estado Retorno</th>
                                <th width="200">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($loanItems as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item->product_nome); ?></strong><br>
                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($item->sku); ?> | Série: <?php echo htmlspecialchars($item->serie); ?></small>
                                </td>
                                <td>
                                    <?php if($item->estado_saida): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($item->estado_saida); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($item->estado_retorno): ?>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($item->estado_retorno); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($item->estado_retorno === null && $loan->status === 'ativo'): ?>
                                        <form action="<?php echo URL_ROOT; ?>/loans/returnItem/<?php echo $loan->id; ?>/<?php echo $item->stock_item_id; ?>" method="post" class="d-inline">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="estado_retorno" class="form-control" placeholder="Estado de retorno" required maxlength="255">
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Confirma a devolução deste item?')">
                                                    <i class="fas fa-check"></i> Devolver
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> Devolvido
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if($loan->status == 'ativo'): ?>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-3"><i class="fas fa-shopping-cart"></i> Converter em Venda</h6>
                        <form action="<?php echo URL_ROOT; ?>/loans/convertToSale/<?php echo $loan->id; ?>" method="POST" class="d-inline-block" onsubmit="return confirm('Tem certeza que deseja converter este empréstimo em venda? Esta ação não pode ser desfeita.')">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select name="seller_id" class="form-select form-select-sm" required>
                                        <option value="">Selecione o Vendedor</option>
                                        <?php foreach($sellers as $seller): ?>
                                            <option value="<?php echo $seller->id; ?>"><?php echo htmlspecialchars($seller->nome); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="channel_id" class="form-select form-select-sm" required>
                                        <option value="">Selecione o Canal</option>
                                        <?php foreach($channels as $channel): ?>
                                            <option value="<?php echo $channel->id; ?>"><?php echo htmlspecialchars($channel->nome); ?></option>
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
                    </div>
                </div>
            </div>
            <?php elseif($loan->status == 'convertido_em_venda'): ?>
            <div class="card-footer">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Este empréstimo foi convertido em venda.
                    <?php if($loan->order_id): ?>
                        <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $loan->order_id; ?>" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-external-link-alt"></i> Ver Pedido
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>