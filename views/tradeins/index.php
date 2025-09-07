<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/tradeins/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Avaliação
        </a>
    </div>
</div>

<?php core\Session::flash('tradein_message'); ?>
<?php core\Session::flash('success_message'); ?>
<?php core\Session::flash('error_message'); ?>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <?php 
    $pendentes = 0;
    $aprovados = 0;
    $reprovados = 0;
    foreach($tradeIns as $ti) {
        switch($ti->status) {
            case 'pendente': $pendentes++; break;
            case 'aprovado': $aprovados++; break;
            case 'reprovado': $reprovados++; break;
        }
    }
    ?>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h5 class="card-title"><?php echo $pendentes; ?></h5>
                <p class="card-text">Pendentes de Aprovação</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title"><?php echo $aprovados; ?></h5>
                <p class="card-text">Aprovados</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title"><?php echo $reprovados; ?></h5>
                <p class="card-text">Reprovados</p>
            </div>
        </div>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Avaliador</th>
            <th>Status</th>
            <th>Data Criação</th>
            <th>Última Atualização</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($tradeIns as $tradeIn): ?>
        <tr class="<?php echo $tradeIn->status == 'pendente' ? 'table-warning' : ''; ?>">
            <td><?php echo $tradeIn->id; ?></td>
            <td><?php echo $tradeIn->customer_nome; ?></td>
            <td><?php echo $tradeIn->avaliador_nome; ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($tradeIn->status){
                            case 'pendente': echo 'bg-warning text-dark'; break;
                            case 'aprovado': echo 'bg-success'; break;
                            case 'reprovado': echo 'bg-danger'; break;
                            case 'creditado': echo 'bg-info'; break;
                        }
                    ?>
                ">
                    <?php echo ucfirst($tradeIn->status); ?>
                </span>
                <?php if($tradeIn->status == 'pendente'): ?>
                    <i class="fas fa-clock text-warning ms-1" title="Aguardando aprovação"></i>
                <?php endif; ?>
            </td>
            <td><?php echo date('d/m/Y H:i', strtotime($tradeIn->created_at)); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($tradeIn->updated_at)); ?></td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/tradeins/show/<?php echo $tradeIn->id; ?>" class="btn btn-sm btn-info">
                    <?php echo $tradeIn->status == 'pendente' ? 'Revisar' : 'Ver'; ?>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>