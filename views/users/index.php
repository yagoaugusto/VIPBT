<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URL_ROOT; ?>/users/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Usuário
        </a>
    </div>
</div>
<?php core\Session::flash('user_message'); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Perfil</th>
            <th>Status</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?php echo $user->id; ?></td>
            <td><?php echo $user->nome; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo ucfirst($user->perfil); ?></td>
            <td>
                <?php if($user->ativo): ?>
                    <span class="badge bg-success">Ativo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inativo</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <a href="<?php echo URL_ROOT; ?>/users/edit/<?php echo $user->id; ?>" class="btn btn-sm btn-secondary">Editar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>