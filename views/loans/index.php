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
<table class="table table-striped">
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
        <tr>
            <td><?php echo $loan->id; ?></td>
            <td><?php echo $loan->customer_nome; ?></td>
            <td><?php echo $loan->vendedor_nome; ?></td>
            <td><?php echo date('d/m/Y', strtotime($loan->data_saida)); ?></td>
            <td><?php echo date('d/m/Y', strtotime($loan->data_prevista_retorno)); ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($loan->status){
                            case 'aberto': echo 'bg-info'; break;
                            case 'devolvido': echo 'bg-success'; break;
                            case 'em_atraso': echo 'bg-danger'; break;
                            case 'convertido_em_venda': echo 'bg-primary'; break;
                        }
                    ?>
                ">
                    <?php echo str_replace('_', ' ', ucfirst($loan->status)); ?>
                </span>
            </td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/loans/show/<?php echo $loan->id; ?>" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>