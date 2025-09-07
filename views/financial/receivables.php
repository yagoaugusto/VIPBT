<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID Pedido</th>
            <th>Código Pedido</th>
            <th>Cliente</th>
            <th>Valor Total</th>
            <th>Valor Recebido</th>
            <th>Valor a Receber</th>
            <th>Última Atualização</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($receivables as $receivable): ?>
        <tr>
            <td><?php echo $receivable->order_id; ?></td>
            <td><?php echo $receivable->public_code; ?></td>
            <td><?php echo $receivable->customer_nome; ?></td>
            <td>R$ <?php echo number_format($receivable->valor_total, 2, ',', '.'); ?></td>
            <td>R$ <?php echo number_format($receivable->valor_recebido, 2, ',', '.'); ?></td>
            <td>R$ <?php echo number_format($receivable->valor_a_receber, 2, ',', '.'); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($receivable->atualizado_em)); ?></td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $receivable->order_id; ?>" class="btn btn-sm btn-info">Ver Pedido</a>
                <?php if($receivable->valor_a_receber > 0): ?>
                    <a href="<?php echo URL_ROOT; ?>/financial/addPayment/<?php echo $receivable->order_id; ?>" class="btn btn-sm btn-success">Receber</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>