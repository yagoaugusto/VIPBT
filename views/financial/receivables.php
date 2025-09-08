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
                        <th>Data Cobrança</th>
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
                        <td style="min-width: 160px;">
                                <input type="date" class="form-control form-control-sm js-charge-date"
                                             data-order-id="<?php echo $receivable->order_id; ?>"
                                             value="<?php echo !empty($receivable->data_cobranca) ? date('Y-m-d', strtotime($receivable->data_cobranca)) : ''; ?>">
                        </td>
                        <td class="js-updated-at" data-order-id="<?php echo $receivable->order_id; ?>">
                                <?php echo date('d/m/Y H:i', strtotime($receivable->atualizado_em)); ?>
                        </td>
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

<script>
document.addEventListener('DOMContentLoaded', function(){
    const inputs = document.querySelectorAll('.js-charge-date');
    inputs.forEach(function(input){
        input.addEventListener('change', function(){
            const orderId = this.dataset.orderId;
            const value = this.value; // '' ou 'YYYY-MM-DD'

            fetch('<?php echo URL_ROOT; ?>/financial/updateChargeDate/' + orderId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ data_cobranca: value || null })
            })
            .then(res => res.json())
            .then(json => {
                if(json && json.success){
                    // Atualiza a coluna "Última Atualização" da mesma linha
                    const updatedCell = document.querySelector('.js-updated-at[data-order-id="' + orderId + '"]');
                    if (updatedCell && json.atualizado_em) {
                        try {
                            const dt = new Date(json.atualizado_em.replace(' ', 'T'));
                            // Formata como dd/mm/yyyy HH:MM
                            const pad = (n) => String(n).padStart(2, '0');
                            const formatted = pad(dt.getDate()) + '/' + pad(dt.getMonth()+1) + '/' + dt.getFullYear() + ' ' + pad(dt.getHours()) + ':' + pad(dt.getMinutes());
                            updatedCell.textContent = formatted;
                        } catch (e) {
                            updatedCell.textContent = json.atualizado_em;
                        }
                    }
                } else {
                    alert(json.message || 'Erro ao salvar data de cobrança.');
                }
            })
            .catch(() => alert('Erro de comunicação ao salvar data de cobrança.'));
        });
    });
});
</script>