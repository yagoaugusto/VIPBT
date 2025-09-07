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
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Avaliador</th>
            <th>Status</th>
            <th>Data Criação</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($tradeIns as $tradeIn): ?>
        <tr>
            <td><?php echo $tradeIn->id; ?></td>
            <td><?php echo $tradeIn->customer_nome; ?></td>
            <td><?php echo $tradeIn->avaliador_nome; ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($tradeIn->status){
                            case 'pendente': echo 'bg-warning'; break;
                            case 'aprovado': echo 'bg-success'; break;
                            case 'reprovado': echo 'bg-danger'; break;
                            case 'creditado': echo 'bg-info'; break;
                        }
                    ?>
                ">
                    <?php echo ucfirst($tradeIn->status); ?>
                </span>
            </td>
            <td><?php echo date('d/m/Y H:i', strtotime($tradeIn->created_at)); ?></td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/tradeins/show/<?php echo $tradeIn->id; ?>" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>