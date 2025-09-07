<div class="row">
    <div class="col-12">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha os dados para criar um novo pedido</p>
            
            <form id="order-form" data-url-root="<?php echo URL_ROOT; ?>">
                <!-- Cabeçalho do Pedido -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Dados do Pedido</legend>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer_id">Cliente: <sup>*</sup></label>
                                <select name="customer_id" id="customer_id" class="form-select">
                                    <option value="">Selecione o cliente</option>
                                    <?php foreach($customers as $customer): ?>
                                        <option value="<?php echo $customer->id; ?>"><?php echo $customer->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="seller_id">Vendedor: <sup>*</sup></label>
                                <select name="seller_id" id="seller_id" class="form-select">
                                    <option value="">Selecione o vendedor</option>
                                    <?php foreach($sellers as $seller): ?>
                                        <option value="<?php echo $seller->id; ?>"><?php echo $seller->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="channel_id">Canal de Venda: <sup>*</sup></label>
                                <select name="channel_id" id="channel_id" class="form-select">
                                    <?php foreach($channels as $channel): ?>
                                        <option value="<?php echo $channel->id; ?>"><?php echo $channel->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="observacao">Observações:</label>
                        <textarea name="observacao" id="observacao" class="form-control"></textarea>
                    </div>
                </fieldset>

                <!-- Itens do Pedido -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Itens do Pedido</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="product_search">Adicionar Produto:</label>
                            <select id="product_search" class="form-select">
                                <option value="">Digite para buscar um produto...</option>
                                <?php foreach($products as $product): ?>
                                    <option value="<?php echo $product->id; ?>" data-price="<?php echo $product->preco; ?>"><?php echo $product->nome; ?> (R$ <?php echo number_format($product->preco, 2, ',', '.'); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" id="add-item-btn" class="btn btn-secondary w-100">Adicionar</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table" id="order-items-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th width="120px">Qtd.</th>
                                <th width="150px">Preço Unit.</th>
                                <th width="150px">Desconto</th>
                                <th width="150px">Subtotal</th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Itens adicionados via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th id="order-subtotal">R$ 0,00</th>
                                <th></th>
                            </tr>
                            <tr id="credits-row" style="display: none;">
                                <th colspan="4" class="text-end text-success">Créditos Trade-in:</th>
                                <th id="order-credits" class="text-success">- R$ 0,00</th>
                                <th></th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="4" class="text-end">Total Final:</th>
                                <th id="order-total">R$ 0,00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>

                <!-- Trade-in / Créditos -->
                <fieldset class="border p-3 mb-3">
                    <legend class="w-auto">Trade-in / Créditos</legend>
                    <div class="row">
                        <div class="col-md-8">
                            <label for="tradein_search">Buscar Trade-in Aprovado:</label>
                            <select id="tradein_search" class="form-select">
                                <option value="">Selecione um trade-in aprovado para aplicar crédito...</option>
                                <!-- Preenchido via AJAX baseado no cliente selecionado -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tradein_credit">Valor Crédito:</label>
                            <input type="text" id="tradein_credit" class="form-control" readonly placeholder="R$ 0,00">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" id="apply-tradein-btn" class="btn btn-info w-100">Aplicar</button>
                        </div>
                    </div>
                    
                    <!-- Trade-ins aplicados -->
                    <div id="applied-tradeins" class="mt-3" style="display: none;">
                        <h6>Créditos Aplicados:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Trade-in</th>
                                    <th>Descrição</th>
                                    <th width="120px">Valor</th>
                                    <th width="50px"></th>
                                </tr>
                            </thead>
                            <tbody id="applied-tradeins-body">
                                <!-- Preenchido via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Créditos:</th>
                                    <th id="total-credits">R$ 0,00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </fieldset>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Criar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script para o formulário de pedido
</script>