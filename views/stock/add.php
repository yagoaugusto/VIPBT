<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Registre uma nova entrada de produtos no estoque.</p>
            
            <?php if(!empty($general_err)): ?>
                <div class="alert alert-danger">
                    <?php echo $general_err; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo URL_ROOT; ?>/stock/add" method="post">
                <div class="form-group mb-3">
                    <label for="product_id">Produto: <sup>*</sup></label>
                    <select name="product_id" class="form-select <?php echo (!empty($product_id_err)) ? 'is-invalid' : ''; ?>">
                        <option value="">Selecione o produto</option>
                        <?php foreach($products as $product): ?>
                            <option value="<?php echo $product->id; ?>" <?php echo ($product_id == $product->id) ? 'selected' : ''; ?>>
                                <?php echo $product->nome; ?> (<?php echo $product->sku; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="invalid-feedback"><?php echo $product_id_err; ?></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="qtd">Quantidade: <sup>*</sup></label>
                            <input type="number" name="qtd" class="form-control <?php echo (!empty($qtd_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $qtd; ?>">
                            <span class="invalid-feedback"><?php echo $qtd_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="custo">Custo por Unidade (R$): <sup>*</sup></label>
                            <input type="text" name="custo" class="form-control <?php echo (!empty($custo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $custo; ?>">
                            <span class="invalid-feedback"><?php echo $custo_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="observacao">Observação:</label>
                    <textarea name="observacao" class="form-control"><?php echo $observacao; ?></textarea>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Registrar Entrada" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/stock" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>