<div class="row">
    <div class="col-12">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha os dados para registrar um novo empréstimo de teste</p>
            
            <form id="loan-form" data-url-root="<?php echo URL_ROOT; ?>">
                <!-- Cabeçalho do Empréstimo -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Dados do Empréstimo</legend>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer_id">Cliente: <sup>*</sup></label>
                                <select name="customer_id" id="customer_id" class="form-select <?php echo (!empty($customer_id_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Selecione o cliente</option>
                                    <?php foreach($customers as $customer): ?>
                                        <option value="<?php echo $customer->id; ?>" <?php echo ($customer_id == $customer->id) ? 'selected' : ''; ?>><?php echo $customer->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $customer_id_err; ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vendedor_user_id">Vendedor:</label>
                                <input type="text" class="form-control" value="<?php echo core\Session::get('user_name'); ?>" disabled>
                                <input type="hidden" name="vendedor_user_id" value="<?php echo core\Session::get('user_id'); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="data_saida">Data de Saída: <sup>*</sup></label>
                                <input type="date" name="data_saida" id="data_saida" class="form-control <?php echo (!empty($data_saida_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $data_saida; ?>">
                                <span class="invalid-feedback"><?php echo $data_saida_err; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_prevista_retorno">Data Prevista de Retorno:</label>
                                <input type="date" name="data_prevista_retorno" id="data_prevista_retorno" class="form-control" value="<?php echo $data_prevista_retorno; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="observacoes">Observações:</label>
                                <textarea name="observacoes" id="observacoes" class="form-control"><?php echo $observacoes; ?></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Itens do Empréstimo -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Itens para Empréstimo</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="stock_item_search">Adicionar Item de Estoque:</label>
                            <select id="stock_item_search" class="form-select <?php echo (!empty($items_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecione um item disponível...</option>
                                <?php foreach($available_stock_items as $item): ?>
                                    <option value="<?php echo $item->id; ?>" data-product-id="<?php echo $item->product_id; ?>"><?php echo $item->product_nome; ?> (SKU: <?php echo $item->sku; ?> | Série: <?php echo $item->serie; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $items_err; ?></span>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_saida">Estado de Saída:</label>
                            <input type="text" id="estado_saida" class="form-control" placeholder="Ex: Novo, Usado, Com marcas">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" id="add-loan-item-btn" class="btn btn-secondary w-100">Adicionar</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table" id="loan-items-table">
                        <thead>
                            <tr>
                                <th>Produto (Série)</th>
                                <th>Estado de Saída</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Itens adicionados via JS -->
                        </tbody>
                    </table>
                </fieldset>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Registrar Empréstimo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- O script para este formulário está em /public/js/main.js -->