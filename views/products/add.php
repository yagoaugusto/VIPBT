<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha o formulário para adicionar um novo produto</p>
            <form action="<?php echo URL_ROOT; ?>/products/add" method="post">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="nome">Nome: <sup>*</sup></label>
                            <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                            <span class="invalid-feedback"><?php echo $nome_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="sku">SKU:</label>
                            <input type="text" name="sku" class="form-control" value="<?php echo $sku; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="brand_id">Marca: <sup>*</sup></label>
                            <select name="brand_id" class="form-select <?php echo (!empty($brand_id_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecione a marca</option>
                                <?php foreach($brands as $brand): ?>
                                    <option value="<?php echo $brand->id; ?>" <?php echo ($brand_id == $brand->id) ? 'selected' : ''; ?>>
                                        <?php echo $brand->nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $brand_id_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="category_id">Categoria: <sup>*</sup></label>
                            <select name="category_id" class="form-select <?php echo (!empty($category_id_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Selecione a categoria</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo ($category_id == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo $category->nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $category_id_err; ?></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="custo">Custo (R$):</label>
                            <input type="text" name="custo" class="form-control" value="<?php echo $custo; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="preco">Preço (R$): <sup>*</sup></label>
                            <input type="text" name="preco" class="form-control <?php echo (!empty($preco_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $preco; ?>">
                            <span class="invalid-feedback"><?php echo $preco_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="tipo_condicao">Condição:</label>
                            <select name="tipo_condicao" class="form-select">
                                <option value="novo" <?php echo ($tipo_condicao == 'novo') ? 'selected' : ''; ?>>Novo</option>
                                <option value="seminovo" <?php echo ($tipo_condicao == 'seminovo') ? 'selected' : ''; ?>>Seminovo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?php echo ($ativo) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ativo">
                        Ativo
                    </label>
                </div>

                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Adicionar" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/products" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>