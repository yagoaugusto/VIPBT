<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/orders/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Pedido
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Código</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Data</th>
            <th>Status</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($orders as $order): ?>
        <tr>
            <td><?php echo $order->id; ?></td>
            <td><?php echo $order->public_code; ?></td>
            <td><?php echo $order->customer_nome; ?></td>
            <td><?php echo $order->seller_nome; ?></td>
            <td><?php echo date('d/m/Y', strtotime($order->data)); ?></td>
            <td>
                <span class="badge bg-primary">
                    <?php echo ucfirst($order->status_pedido); ?>
                </span>
            </td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $order->id; ?>" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>