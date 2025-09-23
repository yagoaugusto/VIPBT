<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
        <p class="text-muted">Listagem de todos os itens físicos em estoque</p>
    </div>
    <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2 header-actions">
        <div class="input-group search-group me-2">
            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="stockSearch" class="form-control" placeholder="Buscar por ID, SKU, Produto, Marca, Série, Grade" autocomplete="off" value="<?php echo htmlspecialchars($q ?? ($_GET['q'] ?? '')); ?>">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpar"><i class="fas fa-times"></i></button>
        </div>
        <a href="<?php echo URL_ROOT; ?>/stock/balances" class="btn btn-info">
            <i class="fas fa-calculator"></i> Saldos de Estoque
        </a>
        <a href="<?php echo URL_ROOT; ?>/stock/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Entrada
        </a>
    </div>
</div>

<?php core\Session::flash('stock_message'); ?>

<div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Dica: digite no campo de busca para filtrar a lista.</small>
        <small id="resultCount" class="text-muted"></small>
    </div>

<table id="stockTable" class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID do Item</th>
            <th>Produto (SKU)</th>
            <th>Marca</th>
            <th>Condição</th>
            <th>Grade</th>
            <th>Status</th>
            <th>Custo de Aquisição</th>
            <th>Preço de Venda</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($stockItems as $item): ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->product_nome . ' (' . $item->sku . ')'; ?></td>
            <td><?php echo $item->brand_nome; ?></td>
            <td><?php echo ucfirst($item->condicao); ?></td>
            <td><?php echo $item->grade; ?></td>
            <td>
                <span class="badge 
                    <?php 
                        switch($item->status){
                            case 'em_estoque': echo 'bg-success'; break;
                            case 'reservado': echo 'bg-warning'; break;
                            case 'emprestado': echo 'bg-info'; break;
                            case 'vendido': echo 'bg-secondary'; break;
                            case 'descartado': echo 'bg-danger'; break;
                        }
                    ?>
                ">
                    <?php echo str_replace('_', ' ', ucfirst($item->status)); ?>
                </span>
            </td>
            <td>R$ <?php echo number_format($item->aquisicao_custo, 2, ',', '.'); ?></td>
            <td>
                <?php if(isset($item->preco_venda) && $item->preco_venda !== null): ?>
                    R$ <?php echo number_format($item->preco_venda, 2, ',', '.'); ?>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td class="text-end">
                <!-- Editar custo -->
                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editCostModal<?php echo $item->id; ?>">
                    <i class="fas fa-edit"></i>
                </button>
                <!-- Excluir: somente em estoque -->
                <?php if($item->status === 'em_estoque'): ?>
                <form action="<?php echo URL_ROOT; ?>/stock/delete/<?php echo $item->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Confirmar exclusão do item <?php echo $item->id; ?>?');">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <!-- Modal de edição de custo -->
        <div class="modal fade" id="editCostModal<?php echo $item->id; ?>" tabindex="-1" aria-labelledby="editCostLabel<?php echo $item->id; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCostLabel<?php echo $item->id; ?>">Editar custo - Item #<?php echo $item->id; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?php echo URL_ROOT; ?>/stock/updateCost/<?php echo $item->id; ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Custo de Aquisição</label>
                                <input type="text" name="aquisicao_custo" class="form-control" value="<?php echo number_format($item->aquisicao_custo, 2, ',', '.'); ?>" required>
                                <small class="text-muted">Use vírgula para centavos (ex: 123,45).</small>
                            </div>
                            <?php if(in_array($item->status, ['vendido','descartado'])): ?>
                                <div class="alert alert-warning">O custo não pode ser editado pois o item está com status "<?php echo $item->status; ?>".</div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" <?php echo in_array($item->status, ['vendido','descartado']) ? 'disabled' : ''; ?>>Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// Filtro client-side na própria tabela
(function(){
    const input = document.getElementById('stockSearch');
    const clearBtn = document.getElementById('clearSearch');
    const table = document.getElementById('stockTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const resultCount = document.getElementById('resultCount');

    const normalize = (s) => (s || '')
            .toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .toLowerCase();

    function applyFilter(){
        const term = normalize(input.value.trim());
        let visible = 0;
        rows.forEach(row => {
            // cache do texto normalizado do row
            const text = row.dataset.search || (row.dataset.search = normalize(row.innerText));
            const match = term === '' || text.indexOf(term) !== -1;
            row.style.display = match ? '' : 'none';
            if(match) visible++;
        });
        resultCount.textContent = visible + ' itens';
    }

    input.addEventListener('input', applyFilter);
    clearBtn.addEventListener('click', () => { input.value = ''; input.focus(); applyFilter(); });
    // aplica ao carregar (respeita valor inicial, inclusive se veio por GET)
    applyFilter();
})();

// Ajusta altura dos botões para igualar à altura do campo de busca
(function(){
    const group = document.querySelector('.search-group');
    function syncHeight(){
        if(!group) return;
        const h = group.offsetHeight || 42;
        document.documentElement.style.setProperty('--header-btn-h', h + 'px');
    }
    window.addEventListener('load', syncHeight);
    window.addEventListener('resize', syncHeight);
    syncHeight();
})();
</script>

<style>
.search-group .input-group-text { border-right: 0; }
.search-group .form-control { border-left: 0; }
/* Largura confortável para a busca */
.search-group{ flex: 1 1 50%; min-width: 420px; max-width: 720px; }
@media (max-width: 767.98px){
    .search-group{ max-width: 100% !important; width: 100%; min-width: 0; }
}
.header-actions .btn{ 
    height: var(--header-btn-h, 42px); 
    display: inline-flex; 
    align-items: center; 
    white-space: nowrap;
}
.header-actions .btn i{ margin-right: .5rem; }
</style>