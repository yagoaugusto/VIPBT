<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Cliente:</strong> <?php echo $order->customer_nome; ?><br>
                        <strong>Telefone:</strong> <?php echo $order->customer_telefone; ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Vendedor:</strong> <?php echo $order->seller_nome; ?><br>
                        <strong>Canal:</strong> <?php echo $order->channel_nome; ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <strong>Data do Pedido:</strong> <?php echo date('d/m/Y', strtotime($order->data)); ?><br>
                        <strong>Status:</strong> <span class="badge bg-primary"><?php echo ucfirst($order->status_pedido); ?></span>
                    </div>
                </div>

                <h5>Itens do Pedido</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th class="text-end">Qtd.</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Desconto</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach($items as $item): 
                            $subtotal = ($item->preco_unit * $item->qtd) - $item->desconto;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $item->sku; ?></td>
                            <td><?php echo $item->product_nome; ?></td>
                            <td class="text-end"><?php echo $item->qtd; ?></td>
                            <td class="text-end">R$ <?php echo number_format($item->preco_unit, 2, ',', '.'); ?></td>
                            <td class="text-end">R$ <?php echo number_format($item->desconto, 2, ',', '.'); ?></td>
                            <td class="text-end">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total do Pedido:</th>
                            <th class="text-end">R$ <?php echo number_format($total, 2, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <?php if(!empty($order->observacao)): ?>
                <div class="mt-4">
                    <strong>Observações:</strong>
                    <p><?php echo nl2br($order->observacao); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo URL_ROOT; ?>/orders" class="btn btn-secondary">Voltar para a Lista</a>
            </div>
        </div>
    </div>
</div>