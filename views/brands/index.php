<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/brands/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Marca
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Status</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($brands as $brand): ?>
        <tr>
            <td><?php echo $brand->id; ?></td>
            <td><?php echo $brand->nome; ?></td>
            <td>
                <?php if($brand->ativo): ?>
                    <span class="badge bg-success">Ativo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inativo</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/brands/edit/<?php echo $brand->id; ?>" class="btn btn-sm btn-secondary">Editar</a>
                <form action="<?php echo URL_ROOT; ?>/brands/delete/<?php echo $brand->id; ?>" method="post" class="d-inline">
                    <input type="submit" value="Excluir" class="btn btn-sm btn-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>