<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/loans/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Empréstimo
        </a>
    </div>
</div>
<?php core\Session::flash('loan_message'); ?>

<!-- Filtros e estatísticas -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-1">Ativos</h6>
                        <h4 class="text-warning"><?php echo count(array_filter($loans, function($l) { return $l->status === 'ativo'; })); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-1">Devolvidos</h6>
                        <h4 class="text-success"><?php echo count(array_filter($loans, function($l) { return $l->status === 'devolvido'; })); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-1">Em Atraso</h6>
                        <h4 class="text-danger"><?php echo count(array_filter($loans, function($l) { return $l->status === 'em_atraso'; })); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-1">Convertidos</h6>
                        <h4 class="text-primary"><?php echo count(array_filter($loans, function($l) { return $l->status === 'convertido_em_venda'; })); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" id="search" class="form-control" placeholder="Buscar por cliente ou vendedor...">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="loans-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Data Saída</th>
                        <th>Prev. Retorno</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($loans as $loan): ?>
                    <tr data-search="<?php echo strtolower($loan->customer_nome . ' ' . $loan->vendedor_nome); ?>">
                        <td>#<?php echo $loan->id; ?></td>
                        <td><?php echo htmlspecialchars($loan->customer_nome); ?></td>
                        <td><?php echo htmlspecialchars($loan->vendedor_nome); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($loan->data_saida)); ?></td>
                        <td>
                            <?php if($loan->data_prevista_retorno): ?>
                                <?php 
                                    $data_retorno = date('d/m/Y', strtotime($loan->data_prevista_retorno));
                                    $is_late = $loan->status === 'ativo' && strtotime($loan->data_prevista_retorno) < strtotime('today');
                                ?>
                                <span class="<?php echo $is_late ? 'text-danger fw-bold' : ''; ?>">
                                    <?php echo $data_retorno; ?>
                                    <?php if($is_late): ?>
                                        <i class="fas fa-exclamation-triangle"></i>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Não definida</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge 
                                <?php 
                                    switch($loan->status){
                                        case 'ativo': echo 'bg-warning text-dark'; break;
                                        case 'devolvido': echo 'bg-success'; break;
                                        case 'em_atraso': echo 'bg-danger'; break;
                                        case 'convertido_em_venda': echo 'bg-primary'; break;
                                        default: echo 'bg-secondary'; break;
                                    }
                                ?>
                            ">
                                <?php 
                                    switch($loan->status){
                                        case 'ativo': echo 'Ativo'; break;
                                        case 'devolvido': echo 'Devolvido'; break;
                                        case 'em_atraso': echo 'Em Atraso'; break;
                                        case 'convertido_em_venda': echo 'Convertido em Venda'; break;
                                        default: echo ucfirst($loan->status); break;
                                    }
                                ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo URL_ROOT; ?>/loans/show/<?php echo $loan->id; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Funcionalidade de busca
document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#loans-table tbody tr');
    
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        if (searchData.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>