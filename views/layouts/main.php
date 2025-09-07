<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<a href="#main-content" class="visually-hidden-focusable btn btn-primary position-absolute top-0 start-0 m-2" style="z-index: 1060;">
    Pular para o conteúdo principal
</a>

<nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo URL_ROOT; ?>">
            <i class="fas fa-tennis-ball me-2"></i><?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <?php if(core\Session::isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCadastros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-database me-1"></i>Cadastros
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarCadastros">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/customers">
                                <i class="fas fa-users me-2"></i>Clientes
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/brands">
                                <i class="fas fa-tags me-2"></i>Marcas
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories">
                                <i class="fas fa-list me-2"></i>Categorias
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/products">
                                <i class="fas fa-box me-2"></i>Produtos
                            </a></li>
                            <?php if(core\Session::get('user_perfil') == 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users">
                                    <i class="fas fa-user-cog me-2"></i>Usuários
                                </a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarOperacoes" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cogs me-1"></i>Operações
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarOperacoes">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/orders">
                                <i class="fas fa-shopping-cart me-2"></i>Pedidos de Venda
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/stock">
                                <i class="fas fa-warehouse me-2"></i>Posição de Estoque
                            </a></li>
                            <?php if(core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'estoquista'): ?>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/loans">
                                    <i class="fas fa-handshake me-2"></i>Empréstimos de Teste
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <?php if(core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'estoquista' || core\Session::get('user_perfil') == 'financeiro'): ?>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/tradeins">
                                    <i class="fas fa-exchange-alt me-2"></i>Avaliações de Trade-in
                                </a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php if(core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'financeiro'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarFinanceiro" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chart-line me-1"></i>Financeiro
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarFinanceiro">
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/financial/receivables">
                                    <i class="fas fa-money-bill-wave me-2"></i>Contas a Receber
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/financial/commissions">
                                    <i class="fas fa-percentage me-2"></i>Comissões
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i>Olá, <?php echo core\Session::get('user_name'); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarUser">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/users/login">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/pages/about">
                        <i class="fas fa-info-circle me-1"></i>Sobre
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="main-content" class="container flex-grow-1 py-4">
    <?php echo $content; ?>
</main>

<footer class="bg-gradient text-white py-4 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-tennis-ball me-2"></i>VIP LOJA BT
                </h5>
                <p class="mb-2">Sua loja especializada em Beach Tennis</p>
                <p class="mb-0">&copy; <?php echo date('Y'); ?> - Todos os direitos reservados</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-3">Contato</h6>
                <p class="mb-1">
                    <i class="fas fa-envelope me-2"></i>contato@viplojabt.com
                </p>
                <p class="mb-1">
                    <i class="fas fa-phone me-2"></i>(11) 99999-9999
                </p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-white me-3">
                        <i class="fab fa-facebook fa-lg"></i>
                    </a>
                    <a href="#" class="text-white">
                        <i class="fab fa-whatsapp fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URL_ROOT; ?>/js/main.js"></script>
</body>
</html>