<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Preencha o formulário para adicionar um novo usuário</p>
            <form action="<?php echo URL_ROOT; ?>/users/add" method="post">
                <div class="form-group mb-3">
                    <label for="nome">Nome: <sup>*</sup></label>
                    <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                    <span class="invalid-feedback"><?php echo $nome_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="senha">Senha: <sup>*</sup></label>
                            <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $senha; ?>">
                            <span class="invalid-feedback"><?php echo $senha_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="confirma_senha">Confirme a Senha: <sup>*</sup></label>
                            <input type="password" name="confirma_senha" class="form-control <?php echo (!empty($confirma_senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirma_senha; ?>">
                            <span class="invalid-feedback"><?php echo $confirma_senha_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="perfil">Perfil:</label>
                    <select name="perfil" id="perfil" class="form-select">
                        <option value="vendedor" <?php echo ($perfil == 'vendedor') ? 'selected' : ''; ?>>Vendedor</option>
                        <option value="admin" <?php echo ($perfil == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="estoquista" <?php echo ($perfil == 'estoquista') ? 'selected' : ''; ?>>Estoquista</option>
                    </select>
                </div>
                <div class="form-group mb-3" id="comissao-field">
                    <label for="comissao">Comissão Padrão (%):</label>
                    <input type="number" name="comissao" class="form-control" value="<?php echo $comissao; ?>" step="0.01">
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Adicionar" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/users" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('perfil').addEventListener('change', function() {
        document.getElementById('comissao-field').style.display = this.value === 'vendedor' ? 'block' : 'none';
    });
    // Trigger the event on page load to set initial state
    document.getElementById('perfil').dispatchEvent(new Event('change'));
</script>