<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha o formulário para adicionar uma nova categoria</p>
            <form action="<?php echo URL_ROOT; ?>/categories/add" method="post">
                <div class="form-group mb-3">
                    <label for="nome">Nome: <sup>*</sup></label>
                    <input type="text" name="nome" class="form-control form-control-lg <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                    <span class="invalid-feedback"><?php echo $nome_err; ?></span>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?php echo ($ativo) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ativo">
                        Ativo
                    </label>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Adicionar" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/categories" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>