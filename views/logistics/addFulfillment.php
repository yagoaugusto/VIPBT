<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2><?php echo $title; ?></h2>
            <p>Pedido: <strong><?php echo $order->public_code; ?></strong> (Cliente: <?php echo $order->customer_nome; ?>)</p>
            <hr>
            <form action="<?php echo URL_ROOT; ?>/logistics/addFulfillment/<?php echo $order->id; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="status">Status da Entrega: <sup>*</sup></label>
                    <select name="status" class="form-select <?php echo (!empty($status_err)) ? 'is-invalid' : ''; ?>">
                        <option value="preparando" <?php echo ($status == 'preparando') ? 'selected' : ''; ?>>Preparando</option>
                        <option value="enviado" <?php echo ($status == 'enviado') ? 'selected' : ''; ?>>Enviado</option>
                        <option value="entregue" <?php echo ($status == 'entregue') ? 'selected' : ''; ?>>Entregue</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $status_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="transportadora">Transportadora:</label>
                    <input type="text" name="transportadora" class="form-control" value="<?php echo $transportadora; ?>">
                </div>
                <div class="form-group mb-3">
                    <label for="codigo_rastreio">Código de Rastreio:</label>
                    <input type="text" name="codigo_rastreio" class="form-control" value="<?php echo $codigo_rastreio; ?>">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="enviado_em">Data de Envio:</label>
                            <input type="datetime-local" name="enviado_em" class="form-control <?php echo (!empty($enviado_em_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $enviado_em; ?>">
                            <span class="invalid-feedback"><?php echo $enviado_em_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="entregue_em">Data de Entrega:</label>
                            <input type="datetime-local" name="entregue_em" class="form-control" value="<?php echo $entregue_em; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="observacoes">Observações:</label>
                    <textarea name="observacoes" class="form-control"><?php echo $observacoes; ?></textarea>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <input type="submit" value="Registrar Expedição" class="btn btn-success btn-block">
                    </div>
                    <div class="col text-end">
                        <a href="<?php echo URL_ROOT; ?>/orders/show/<?php echo $order->id; ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
            <hr>
            <h5>Histórico de Expedição</h5>
            <?php if(empty($fulfillments)): ?>
                <p>Nenhum registro de expedição para este pedido.</p>
            <?php else: ?>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Transportadora</th>
                            <th>Rastreio</th>
                            <th>Enviado em</th>
                            <th>Entregue em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($fulfillments as $fulfillment): ?>
                        <tr>
                            <td><?php echo ucfirst($fulfillment->status); ?></td>
                            <td><?php echo $fulfillment->transportadora; ?></td>
                            <td><?php echo $fulfillment->codigo_rastreio; ?></td>
                            <td><?php echo $fulfillment->enviado_em ? date('d/m/Y H:i', strtotime($fulfillment->enviado_em)) : 'N/A'; ?></td>
                            <td><?php echo $fulfillment->entregue_em ? date('d/m/Y H:i', strtotime($fulfillment->entregue_em)) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>