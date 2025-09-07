<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/customers/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Cliente
        </a>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>Cidade</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($customers as $customer): ?>
        <tr>
            <td><?php echo $customer->id; ?></td>
            <td><?php echo $customer->nome; ?></td>
            <td><?php echo $customer->telefone; ?></td>
            <td><?php echo $customer->cidade; ?></td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/customers/edit/<?php echo $customer->id; ?>" class="btn btn-sm btn-secondary">Editar</a>
                <form action="<?php echo URL_ROOT; ?>/customers/delete/<?php echo $customer->id; ?>" method="post" class="d-inline">
                    <input type="submit" value="Excluir" class="btn btn-sm btn-danger">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>