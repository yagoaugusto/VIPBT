<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/products/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Produto
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nome</th>
            <th>Marca</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Status</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($products as $product): ?>
        <tr>
            <td><?php echo $product->sku; ?></td>
            <td><?php echo $product->nome; ?></td>
            <td><?php echo $product->brand_nome; ?></td>
            <td><?php echo $product->category_nome; ?></td>
            <td>—</td>
            <td>
                <?php if($product->ativo): ?>
                    <span class="badge bg-success">Ativo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inativo</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/products/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-secondary">Editar</a>
                <form action="<?php echo URL_ROOT; ?>/products/delete/<?php echo $product->id; ?>" method="post" class="d-inline">
                    <input type="submit" value="Excluir" class="btn btn-sm btn-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>