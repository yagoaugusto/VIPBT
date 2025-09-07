<div class="row">
    <div class="col-12">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha os dados para registrar uma nova avaliação de trade-in</p>
            
            <form id="tradein-form" data-url-root="<?php echo URL_ROOT; ?>">
                <!-- Cabeçalho do Trade-in -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Dados da Avaliação</legend>
                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="avaliador_user_id">Avaliador:</label>
                                <input type="text" class="form-control" value="<?php echo core\Session::get('user_name'); ?>" disabled>
                                <input type="hidden" name="avaliador_user_id" value="<?php echo core\Session::get('user_id'); ?>">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Itens do Trade-in -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Itens para Avaliação</legend>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="item_brand_id">Marca:</label>
                            <select id="item_brand_id" class="form-select">
                                <option value="">Selecione a marca</option>
                                <?php foreach($brands as $brand): ?>
                                    <option value="<?php echo $brand->id; ?>"><?php echo $brand->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="item_product_model_id">Modelo (Produto Existente):</label>
                            <select id="item_product_model_id" class="form-select">
                                <option value="">Selecione o modelo</option>
                                <?php foreach($products as $product): ?>
                                    <option value="<?php echo $product->id; ?>"><?php echo $product->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="item_modelo_texto">Modelo (Texto Livre):</label>
                            <input type="text" id="item_modelo_texto" class="form-control" placeholder="Ex: Raquete X, Bola Y">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="item_grade">Grade:</label>
                            <select id="item_grade" class="form-select">
                                <option value="">Selecione</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="item_serie">Série:</label>
                            <input type="text" id="item_serie" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="item_avaliacao_valor">Valor Avaliado (R$): <sup>*</sup></label>
                            <input type="number" id="item_avaliacao_valor" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="item_valor_creditado">Valor Creditado (R$): <sup>*</sup></label>
                            <input type="number" id="item_valor_creditado" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="item_observacoes">Observações do Item:</label>
                        <textarea id="item_observacoes" class="form-control"></textarea>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" id="add-tradein-item-btn" class="btn btn-secondary">Adicionar Item</button>
                    </div>
                    <hr>
                    <table class="table" id="tradein-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Grade</th>
                                <th>Série</th>
                                <th>Valor Avaliado</th>
                                <th>Valor Creditado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Itens adicionados via JS -->
                        </tbody>
                    </table>
                </fieldset>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Registrar Avaliação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script para o formulário de trade-in
</script>