<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha o formulário para editar o cliente</p>
            <form action="<?php echo URL_ROOT; ?>/customers/edit/<?php echo $id; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="nome">Nome: <sup>*</sup></label>
                    <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                    <span class="invalid-feedback"><?php echo $nome_err; ?></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="telefone">Telefone:</label>
                            <input type="text" name="telefone" class="form-control" value="<?php echo $telefone; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="cidade">Cidade:</label>
                            <input type="text" name="cidade" class="form-control" value="<?php echo $cidade; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="raquete_entrada_bool" id="raquete_entrada_bool" <?php echo ($raquete_entrada_bool) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="raquete_entrada_bool">
                        Tem raquete de entrada?
                    </label>
                </div>
                <div class="form-group mb-3">
                    <label for="raquete_entrada_produto_id">Raquete de Entrada (Produto):</label>
                    <select name="raquete_entrada_produto_id" class="form-select">
                        <option value="">Selecione a raquete</option>
                        <?php foreach($products as $product): ?>
                            <option value="<?php echo $product->id; ?>" <?php echo ($raquete_entrada_produto_id == $product->id) ? 'selected' : ''; ?>>
                                <?php echo $product->nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Salvar Alterações" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/customers" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>