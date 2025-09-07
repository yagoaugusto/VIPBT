<div class="row">
    <div class="col-12">
        <div class="card mt-3">
            <div class="card-header">
                <h2><?php echo $title; ?></h2>
                <p class="mb-0 text-muted">Preencha os dados para registrar um novo empréstimo de teste</p>
            </div>
            <div class="card-body">
                <form id="loan-form" data-url-root="<?php echo URL_ROOT; ?>">
                    <!-- Cabeçalho do Empréstimo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user"></i> Dados do Empréstimo
                            </h5>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer_id" class="form-label">Cliente: <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select <?php echo (!empty($customer_id_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Selecione o cliente</option>
                                    <?php foreach($customers as $customer): ?>
                                        <option value="<?php echo $customer->id; ?>" <?php echo ($customer_id == $customer->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer->nome); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?php echo $customer_id_err; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vendedor_display" class="form-label">Vendedor:</label>
                                <input type="text" id="vendedor_display" class="form-control" value="<?php echo htmlspecialchars(core\Session::get('user_name')); ?>" disabled>
                                <input type="hidden" name="vendedor_user_id" value="<?php echo core\Session::get('user_id'); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="data_saida" class="form-label">Data de Saída: <span class="text-danger">*</span></label>
                                <input type="date" name="data_saida" id="data_saida" class="form-control <?php echo (!empty($data_saida_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $data_saida; ?>">
                                <div class="invalid-feedback"><?php echo $data_saida_err; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_prevista_retorno" class="form-label">Data Prevista de Retorno:</label>
                                <input type="date" name="data_prevista_retorno" id="data_prevista_retorno" class="form-control" value="<?php echo $data_prevista_retorno; ?>">
                                <div class="form-text">Data estimada para devolução dos itens</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="observacoes" class="form-label">Observações:</label>
                                <textarea name="observacoes" id="observacoes" class="form-control" rows="3" placeholder="Observações adicionais sobre o empréstimo..."><?php echo $observacoes; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Itens do Empréstimo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-box"></i> Itens para Empréstimo
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <label for="stock_item_search" class="form-label">Adicionar Item de Estoque:</label>
                            <select id="stock_item_search" class="form-select <?php echo (!empty($items_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecione um item disponível...</option>
                                <?php foreach($available_stock_items as $item): ?>
                                    <option value="<?php echo $item->id; ?>" data-product-id="<?php echo $item->product_id; ?>">
                                        <?php echo htmlspecialchars($item->product_nome); ?> 
                                        (SKU: <?php echo htmlspecialchars($item->sku); ?> | Série: <?php echo htmlspecialchars($item->serie); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?php echo $items_err; ?></div>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_saida" class="form-label">Estado de Saída:</label>
                            <input type="text" id="estado_saida" class="form-control" placeholder="Ex: Novo, Usado, Com marcas" maxlength="255">
                            <div class="form-text">Condição do item na saída</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" id="add-loan-item-btn" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Itens Selecionados</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="loan-items-table">
                                            <thead>
                                                <tr>
                                                    <th>Produto (Série)</th>
                                                    <th>Estado de Saída</th>
                                                    <th width="80"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Itens adicionados via JS -->
                                            </tbody>
                                        </table>
                                        <div id="no-items-message" class="text-center text-muted py-3">
                                            Nenhum item adicionado ainda
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo URL_ROOT; ?>/loans" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save"></i> Registrar Empréstimo
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- O script para este formulário está em /public/js/main.js -->