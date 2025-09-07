<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <?php core\Session::flash('register_success'); ?>
            <h2><?php echo $title; ?></h2>
            <p>Por favor, preencha suas credenciais para fazer login</p>
            <form action="<?php echo URL_ROOT; ?>/users/login" method="post">
                <div class="form-group mb-3">
                    <label for="email">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="senha">Senha: <sup>*</sup></label>
                    <input type="password" name="senha" class="form-control form-control-lg <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $senha; ?>">
                    <span class="invalid-feedback"><?php echo $senha_err; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Login" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>