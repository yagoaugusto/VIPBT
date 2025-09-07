<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Pedido: <strong><?php echo $order->public_code; ?></strong> (Cliente: <?php echo $order->customer_nome; ?>)</p>
            <p>Valor Total: R$ <?php echo number_format($receivable->valor_total, 2, ',', '.'); ?></p>
            <p>Valor Recebido: R$ <?php echo number_format($receivable->valor_recebido, 2, ',', '.'); ?></p>
            <p>Valor a Receber: R$ <?php echo number_format($receivable->valor_a_receber, 2, ',', '.'); ?></p>
            <hr>
            <form action="<?php echo URL_ROOT; ?>/financial/addPayment/<?php echo $order->id; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="forma">Forma de Pagamento: <sup>*</sup></label>
                    <select name="forma" class="form-select">
                        <option value="pix" <?php echo ($forma == 'pix') ? 'selected' : ''; ?>>PIX</option>
                        <option value="cartao" <?php echo ($forma == 'cartao') ? 'selected' : ''; ?>>Cartão</option>
                        <option value="dinheiro" <?php echo ($forma == 'dinheiro') ? 'selected' : ''; ?>>Dinheiro</option>
                        <option value="boleto" <?php echo ($forma == 'boleto') ? 'selected' : ''; ?>>Boleto</option>
                        <option value="transferencia" <?php echo ($forma == 'transferencia') ? 'selected' : ''; ?>>Transferência</option>
                        <option value="outros" <?php echo ($forma == 'outros') ? 'selected' : ''; ?>>Outros</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="valor">Valor: <sup>*</sup></label>
                    <input type="text" name="valor" class="form-control <?php echo (!empty($valor_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $valor; ?>">
                    <span class="invalid-feedback"><?php echo $valor_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="data">Data do Pagamento: <sup>*</sup></label>
                    <input type="date" name="data" class="form-control <?php echo (!empty($data_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $data; ?>">
                    <span class="invalid-feedback"><?php echo $data_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="status_pagamento">Status do Pagamento:</label>
                    <select name="status_pagamento" class="form-select">
                        <option value="pendente" <?php echo ($status_pagamento == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="parcial" <?php echo ($status_pagamento == 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                        <option value="pago" <?php echo ($status_pagamento == 'pago') ? 'selected' : ''; ?>>Pago</option>
                    </select>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Registrar Pagamento" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $order->id; ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
            <hr>
            <h5>Histórico de Pagamentos</h5>
            <?php if(empty($payments)): ?>
                <p>Nenhum pagamento registrado para este pedido.</p>
            <?php else: ?>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Forma</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment->id; ?></td>
                            <td><?php echo ucfirst($payment->forma); ?></td>
                            <td>R$ <?php echo number_format($payment->valor, 2, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($payment->data)); ?></td>
                            <td><?php echo ucfirst($payment->status_pagamento); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>