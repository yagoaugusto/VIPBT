<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?php echo URL_ROOT; ?>"><?php echo SITE_NAME; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>">Home</a>
                </li>
                <?php if(core\Session::isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCadastros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cadastros
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarCadastros">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/customers">Clientes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/brands">Marcas</a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories">Categorias</a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/products">Produtos</a></li>
                            <?php if(core\Session::get('user_perfil') == 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users">Usuários</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarOperacoes" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Operações
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarOperacoes">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/orders">Pedidos de Venda</a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/stock">Posição de Estoque</a></li>
                            <?php if(core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'estoquista'): ?>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/loans">Empréstimos de Teste</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/tradeins">Avaliações de Trade-in</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php if(core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'financeiro'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarFinanceiro" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Financeiro
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarFinanceiro">
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/financial/receivables">Contas a Receber</a></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/financial/commissions">Comissões</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Olá, <?php echo core\Session::get('user_name'); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarUser">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/users/login">Login</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/pages/about">Sobre</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container">
    <?php echo $content; ?>
</main>

<footer class="mt-5 p-4 bg-dark text-white text-center">
    <p>VIP LOJA BT &copy; <?php echo date('Y'); ?></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URL_ROOT; ?>/js/main.js"></script>
</body>
</html>