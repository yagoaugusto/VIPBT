<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">
            <i class="fas fa-tennis-ball me-3"></i><?php echo $title; ?>
        </h1>
        <p class="hero-subtitle"><?php echo $description; ?></p>
    </div>
</div>

<div class="quick-access-section">
    <h2 class="quick-access-title text-gradient">
        <i class="fas fa-rocket me-2"></i>Acesso Rápido às Funcionalidades
    </h2>
    
    <div class="quick-access-cards">
        <?php if(class_exists('core\\Session') && core\Session::isLoggedIn()): ?>
            <!-- Cadastros -->
            <a href="<?php echo URL_ROOT; ?>/customers" class="quick-access-card animate-fade-in-up">
                <div class="quick-access-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Clientes</h4>
                <p>Gerencie sua base de clientes com facilidade</p>
            </a>
            
            <a href="<?php echo URL_ROOT; ?>/products" class="quick-access-card animate-fade-in-up">
                <div class="quick-access-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h4>Produtos</h4>
                <p>Controle seu catálogo de produtos</p>
            </a>
            
            <!-- Operações -->
            <a href="<?php echo URL_ROOT; ?>/orders" class="quick-access-card animate-fade-in-up">
                <div class="quick-access-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h4>Pedidos de Venda</h4>
                <p>Crie e gerencie pedidos de venda</p>
            </a>
            
            <a href="<?php echo URL_ROOT; ?>/stock" class="quick-access-card animate-fade-in-up">
                <div class="quick-access-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <h4>Estoque</h4>
                <p>Acompanhe a posição do seu estoque</p>
            </a>
            
            <?php if(class_exists('core\\Session') && (core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'estoquista' || core\Session::get('user_perfil') == 'financeiro')): ?>
                <a href="<?php echo URL_ROOT; ?>/tradeins" class="quick-access-card animate-fade-in-up">
                    <div class="quick-access-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h4>Trade-in</h4>
                    <p>Avaliações de produtos usados</p>
                </a>
            <?php endif; ?>
            
            <?php if(class_exists('core\\Session') && (core\Session::get('user_perfil') == 'admin' || core\Session::get('user_perfil') == 'financeiro')): ?>
                <a href="<?php echo URL_ROOT; ?>/financial/receivables" class="quick-access-card animate-fade-in-up">
                    <div class="quick-access-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h4>Financeiro</h4>
                    <p>Contas a receber e comissões</p>
                </a>
            <?php endif; ?>
            
        <?php else: ?>
            <a href="<?php echo URL_ROOT; ?>/users/login" class="quick-access-card animate-fade-in-up">
                <div class="quick-access-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h4>Fazer Login</h4>
                <p>Entre no sistema para acessar as funcionalidades</p>
            </a>
            
            
        <?php endif; ?>
    </div>
    
    <?php if(class_exists('core\\Session') && core\Session::isLoggedIn()): ?>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-soft">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Status do Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-success mb-2">
                            <i class="fas fa-check-circle me-2"></i>Sistema operacional
                        </p>
                        <p class="text-info mb-2">
                            <i class="fas fa-user me-2"></i>Usuário: <?php echo class_exists('core\\Session') ? core\Session::get('user_name') : 'Usuário'; ?>
                        </p>
                        <p class="text-primary mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Perfil: <?php echo class_exists('core\\Session') ? ucfirst(core\Session::get('user_perfil')) : 'Usuário'; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-soft">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Dicas Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use o menu de navegação acima para acessar todas as funcionalidades
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Clique nos cartões acima para acesso rápido às principais áreas
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                Mantenha seus dados sempre atualizados
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>