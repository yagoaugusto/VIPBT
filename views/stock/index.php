<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/stock/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Entrada
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID do Item</th>
            <th>Produto (SKU)</th>
            <th>Marca</th>
            <th>Condição</th>
            <th>Grade</th>
            <th>Status</th>
            <th>Custo de Aquisição</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($stockItems as $item): ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->product_nome . ' (' . $item->sku . ')'; ?></td>
            <td><?php echo $item->brand_nome; ?></td>
            <td><?php echo ucfirst($item->condicao); ?></td>
            <td><?php echo $item->grade; ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($item->status){
                            case 'em_estoque': echo 'bg-success'; break;
                            case 'reservado': echo 'bg-warning'; break;
                            case 'emprestado': echo 'bg-info'; break;
                            case 'vendido': echo 'bg-secondary'; break;
                            case 'descartado': echo 'bg-danger'; break;
                        }
                    ?>
                ">
                    <?php echo str_replace('_', ' ', ucfirst($item->status)); ?>
                </span>
            </td>
            <td>R$ <?php echo number_format($item->aquisicao_custo, 2, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>