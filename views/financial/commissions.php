<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Pedido</th>
            <th>Vendedor</th>
            <th>Base de Cálculo</th>
            <th>%</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($comissions as $comission): ?>
        <tr>
            <td><?php echo $comission->id; ?></td>
            <td><a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $comission->order_id; ?>"><?php echo $comission->public_code; ?></a></td>
            <td><?php echo $comission->seller_nome; ?></td>
            <td>R$ <?php echo number_format($comission->base_calculo, 2, ',', '.'); ?></td>
            <td>R$ <?php echo number_format($comission->perc, 2, ',', '.'); ?>%</td>
            <td>R$ <?php echo number_format($comission->valor, 2, ',', '.'); ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($comission->status){
                            case 'a_apurar': echo 'bg-warning'; break;
                            case 'liberada': echo 'bg-info'; break;
                            case 'paga': echo 'bg-success'; break;
                        }
                    ?>
                ">
                    <?php echo str_replace('_', ' ', ucfirst($comission->status)); ?>
                </span>
            </td>
            <td><?php echo date('d/m/Y', strtotime($comission->created_at)); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>