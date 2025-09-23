<div class="row mb-3">
    <div class="col-md-6">
        <h1><?php echo $title; ?></h1>
        <p class="text-muted">Saldos calculados com base nas movimentações de estoque</p>
    </div>
    <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2 header-actions">
        <div class="input-group search-group me-2">
            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="balancesSearch" class="form-control" placeholder="Buscar por Produto, SKU, Marca ou Tipo" autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clearBalancesSearch" title="Limpar"><i class="fas fa-times"></i></button>
        </div>
        <a href="<?php echo URL_ROOT; ?>/stock" class="btn btn-secondary">
            <i class="fas fa-list"></i> Itens Físicos
        </a>
        <a href="<?php echo URL_ROOT; ?>/stock/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Entrada
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Dica: use a busca para filtrar rapidamente.</small>
            <small id="balancesResultCount" class="text-muted"></small>
        </div>
        <table id="balancesTable" class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Produto (SKU)</th>
                    <th>Marca</th>
                    <th>Tipo</th>
                    <th class="text-center">Em Estoque</th>
                    <th class="text-center">Reservados</th>
                    <th class="text-center">Emprestados</th>
                    <th class="text-center">Vendidos</th>
                    <th class="text-center">Total Físico</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Calcula totais
                    $tot_disponiveis = 0; $tot_reservados = 0; $tot_emprestados = 0; $tot_vendidos = 0; $tot_fisicos = 0;
                    if(!empty($stockBalances)){
                        foreach($stockBalances as $b){
                            $tot_disponiveis += (int)($b->itens_disponiveis ?: 0);
                            $tot_reservados += (int)($b->itens_reservados ?: 0);
                            $tot_emprestados += (int)($b->itens_emprestados ?: 0);
                            $tot_vendidos += (int)($b->itens_vendidos ?: 0);
                            $tot_fisicos += (int)($b->itens_fisicos ?: 0);
                        }
                    }
                ?>
                <!-- Linha de Totais -->
                <tr class="total-row" data-static="total" style="position: sticky; top: 0; z-index: 1;">
                    <td colspan="3"><strong class="text-uppercase">Total</strong></td>
                    <td class="text-center"><span class="badge bg-success"><?php echo $tot_disponiveis; ?></span></td>
                    <td class="text-center"><span class="badge bg-warning"><?php echo $tot_reservados; ?></span></td>
                    <td class="text-center"><span class="badge bg-info"><?php echo $tot_emprestados; ?></span></td>
                    <td class="text-center"><span class="badge bg-secondary"><?php echo $tot_vendidos; ?></span></td>
                    <td class="text-center"><strong><?php echo $tot_fisicos; ?></strong></td>
                </tr>
                <?php if(empty($stockBalances)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Nenhum produto com estoque encontrado</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($stockBalances as $balance): ?>
                    <tr>
                        <td>
                            <strong><?php echo $balance->product_nome; ?></strong><br>
                            <small class="text-muted"><?php echo $balance->sku; ?></small>
                        </td>
                        <td><?php echo $balance->brand_nome; ?></td>
                        <td>
                            <span class="badge <?php echo $balance->tipo_condicao == 'novo' ? 'bg-success' : 'bg-info'; ?>">
                                <?php echo ucfirst($balance->tipo_condicao); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">
                                <?php echo $balance->itens_disponiveis ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning">
                                <?php echo $balance->itens_reservados ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                <?php echo $balance->itens_emprestados ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                <?php echo $balance->itens_vendidos ?: 0; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <strong><?php echo $balance->itens_fisicos ?: 0; ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Legenda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Em Estoque:</strong> Itens físicos disponíveis para venda</p>
                        <p><strong>Reservados:</strong> Itens físicos reservados para vendas específicas</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Emprestados:</strong> Itens físicos em empréstimo de teste</p>
                        <p><strong>Vendidos:</strong> Itens físicos já vendidos</p>
                        <p><strong>Total Físico:</strong> Soma de todos os itens físicos cadastrados</p>
                    </div>
                </div>
            </div>

                    <script>
                    // Filtro client-side na tabela de saldos
                    (function(){
                        const input = document.getElementById('balancesSearch');
                        const clearBtn = document.getElementById('clearBalancesSearch');
                        const table = document.getElementById('balancesTable');
                        if(!input || !table) return;
                        const tbody = table.querySelector('tbody');
                        const allRows = Array.from(tbody.querySelectorAll('tr'));
                        const dataRows = allRows.filter(r => !r.hasAttribute('data-static'));
                        const totalRow = allRows.find(r => r.hasAttribute('data-static'));
                        const resultCount = document.getElementById('balancesResultCount');

                        const normalize = (s) => (s || '')
                            .toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                            .replace(/\s+/g, ' ').toLowerCase();

                        function applyFilter(){
                            const term = normalize(input.value.trim());
                            let visible = 0;
                            dataRows.forEach(row => {
                                const text = row.dataset.search || (row.dataset.search = normalize(row.innerText));
                                const match = term === '' || text.indexOf(term) !== -1;
                                row.style.display = match ? '' : 'none';
                                if(match) visible++;
                            });
                            if(totalRow) totalRow.style.display = '';
                            resultCount.textContent = visible + ' itens';
                        }

                        input.addEventListener('input', applyFilter);
                        clearBtn.addEventListener('click', () => { input.value = ''; input.focus(); applyFilter(); });
                        applyFilter();
                    })();

                    // Igualar altura dos botões com a busca
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
                    .search-group .input-group-text{ border-right:0; }
                    .search-group .form-control{ border-left:0; }
                    .search-group{ flex:1 1 50%; min-width: 420px; max-width: 720px; }
                    @media (max-width: 767.98px){ .search-group{ max-width:100% !important; width:100%; min-width:0; } }
                    .header-actions .btn{ height: var(--header-btn-h, 42px); display:inline-flex; align-items:center; white-space:nowrap; }
                    .header-actions .btn i{ margin-right:.5rem; }
                    </style>
                    <style>
                    /* Estilo diferenciado para a linha de Total (suave e harmonizado) */
                    /* Neutraliza o listrado do Bootstrap e aplica cor própria */
                    #balancesTable.table-striped > tbody > tr.total-row,
                    #balancesTable.table-striped > tbody > tr.total-row:nth-of-type(odd){
                        --bs-table-accent-bg: transparent !important;
                        background-color: #f5f8ff !important; /* aplica no <tr> */
                    }
                    /* aplica também diretamente nas células para vencer regras do striped */
                    #balancesTable.table-striped > tbody > tr.total-row > td,
                    #balancesTable.table-striped > tbody > tr.total-row:nth-of-type(odd) > td{
                        background-color: #f5f8ff !important;
                    }
                    #balancesTable tbody tr.total-row > td{ 
                        font-weight:600; 
                        border-top: none !important;
                        border-bottom: none !important;
                        background-clip: padding-box;
                    }
                    #balancesTable tbody tr.total-row > td:first-child{ 
                        border-left: 4px solid #a7c8ff; 
                        border-top-left-radius: 6px; 
                        border-bottom-left-radius: 6px; 
                    }
                    /* Ajusta a borda superior da primeira linha de dados após o total para criar separação visual */
                    #balancesTable tbody tr.total-row + tr > td{ border-top: 2px solid #d6e6ff !important; }
                    </style>
        </div>
    </div>
</div>