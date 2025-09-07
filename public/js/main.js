document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('order-form');
    if (!orderForm) {
        return;
    }

    const urlRoot = orderForm.dataset.urlRoot;
    const productSearch = document.getElementById('product_search');
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsTableBody = document.querySelector('#order-items-table tbody');
    const orderTotalEl = document.getElementById('order-total');

    let orderItems = [];

    // Adicionar item ao pedido
    addItemBtn.addEventListener('click', function() {
        const selectedOption = productSearch.options[productSearch.selectedIndex];
        if (!selectedOption.value) {
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.text.split(' (R$')[0];
        const productPrice = parseFloat(selectedOption.dataset.price);

        if (orderItems.find(item => item.id === productId)) {
            alert('Este produto já foi adicionado.');
            return;
        }

        const item = {
            id: productId,
            name: productName,
            price: productPrice,
            quantity: 1,
            discount: 0
        };

        orderItems.push(item);
        renderItemsTable();
    });

    // Renderiza a tabela de itens
    function renderItemsTable() {
        itemsTableBody.innerHTML = '';
        let total = 0;

        orderItems.forEach((item, index) => {
            const subtotal = (item.price * item.quantity) - item.discount;
            total += subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td><input type="number" class="form-control item-qty" data-index="${index}" value="${item.quantity}" min="1" step="1"></td>
                <td><input type="number" class="form-control item-price" data-index="${index}" value="${item.price.toFixed(2)}" step="0.01"></td>
                <td><input type="number" class="form-control item-discount" data-index="${index}" value="${item.discount.toFixed(2)}" step="0.01" min="0"></td>
                <td class="subtotal">R$ ${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item" data-index="${index}">&times;</button></td>
            `;
            itemsTableBody.appendChild(row);
        });

        orderTotalEl.textContent = `R$ ${total.toFixed(2)}`;
    }

    // Atualiza quantidade, preço ou desconto
    itemsTableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price') || e.target.classList.contains('item-discount')) {
            const index = e.target.dataset.index;
            const newQty = parseFloat(document.querySelector(`.item-qty[data-index="${index}"]`).value) || 1;
            const newPrice = parseFloat(document.querySelector(`.item-price[data-index="${index}"]`).value) || 0;
            const newDiscount = parseFloat(document.querySelector(`.item-discount[data-index="${index}"]`).value) || 0;
            
            orderItems[index].quantity = newQty;
            orderItems[index].price = newPrice;
            orderItems[index].discount = newDiscount;
            
            renderItemsTable();
        }
    });

    // Remover item
    itemsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const index = e.target.dataset.index;
            orderItems.splice(index, 1);
            renderItemsTable();
        }
    });

    // Submeter o formulário
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const orderData = {
            customer_id: document.getElementById('customer_id').value,
            seller_id: document.getElementById('seller_id').value,
            channel_id: document.getElementById('channel_id').value,
            observacao: document.getElementById('observacao').value,
            items: orderItems.map(item => ({
                id: item.id,
                qtd: item.quantity,
                preco: item.price,
                desconto: item.discount
            }))
        };
        
        if (!orderData.customer_id || !orderData.seller_id || orderItems.length === 0) {
            alert('Por favor, preencha o cliente, o vendedor e adicione pelo menos um item.');
            return;
        }

        fetch(`${urlRoot}/orders/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `${urlRoot}/orders/show/${data.order_id}`;
            } else {
                alert('Erro ao criar o pedido: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocorreu um erro de comunicação.');
        });
    });
});

// Script para o formulário de trade-in
document.addEventListener('DOMContentLoaded', function() {
    const tradeInForm = document.getElementById('tradein-form');
    if (!tradeInForm) {
        return;
    }

    const urlRoot = tradeInForm.dataset.urlRoot;
    const addTradeInItemBtn = document.getElementById('add-tradein-item-btn');
    const tradeInItemsTableBody = document.querySelector('#tradein-items-table tbody');

    let tradeInItems = [];

    // Adicionar item de trade-in
    addTradeInItemBtn.addEventListener('click', function() {
        const itemBrandId = document.getElementById('item_brand_id').value;
        const itemProductModelId = document.getElementById('item_product_model_id').value;
        const itemModeloTexto = document.getElementById('item_modelo_texto').value;
        const itemGrade = document.getElementById('item_grade').value;
        const itemSerie = document.getElementById('item_serie').value;
        const itemAvaliacaoValor = parseFloat(document.getElementById('item_avaliacao_valor').value);
        const itemValorCreditado = parseFloat(document.getElementById('item_valor_creditado').value);
        const itemObservacoes = document.getElementById('item_observacoes').value;

        // Validação básica
        if (!itemAvaliacaoValor || !itemValorCreditado) {
            alert('Por favor, preencha o Valor Avaliado e o Valor Creditado.');
            return;
        }

        const item = {
            brand_id: itemBrandId || null,
            product_model_id: itemProductModelId || null,
            modelo_texto: itemModeloTexto,
            grade: itemGrade || null,
            serie: itemSerie,
            avaliacao_valor: itemAvaliacaoValor,
            valor_creditado: itemValorCreditado,
            observacoes: itemObservacoes
        };

        tradeInItems.push(item);
        renderTradeInItemsTable();
        clearTradeInItemForm();
    });

    // Renderiza a tabela de itens de trade-in
    function renderTradeInItemsTable() {
        tradeInItemsTableBody.innerHTML = '';
        tradeInItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.modelo_texto || (item.product_model_id ? document.querySelector('#item_product_model_id option[value="' + item.product_model_id + '"]').textContent : 'N/A')}</td>
                <td>${item.grade || 'N/A'}</td>
                <td>${item.serie || 'N/A'}</td>
                <td>R$ ${item.avaliacao_valor.toFixed(2)}</td>
                <td>R$ ${item.valor_creditado.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-tradein-item" data-index="${index}">&times;</button></td>
            `;
            tradeInItemsTableBody.appendChild(row);
        });
    }

    // Limpa o formulário de item de trade-in
    function clearTradeInItemForm() {
        document.getElementById('item_brand_id').value = '';
        document.getElementById('item_product_model_id').value = '';
        document.getElementById('item_modelo_texto').value = '';
        document.getElementById('item_grade').value = '';
        document.getElementById('item_serie').value = '';
        document.getElementById('item_avaliacao_valor').value = '';
        document.getElementById('item_valor_creditado').value = '';
        document.getElementById('item_observacoes').value = '';
    }

    // Remover item de trade-in
    tradeInItemsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tradein-item')) {
            const index = e.target.dataset.index;
            tradeInItems.splice(index, 1);
            renderTradeInItemsTable();
        }
    });

    // Submeter o formulário de trade-in
    tradeInForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const tradeInFormData = {
            customer_id: document.getElementById('customer_id').value,
            items: tradeInItems
        };

        if (!tradeInFormData.customer_id || tradeInItems.length === 0) {
            alert('Por favor, selecione o cliente e adicione pelo menos um item de trade-in.');
            return;
        }

        // Envia os dados como FormData para que o PHP possa usar $_POST
        const formData = new FormData();
        formData.append('customer_id', tradeInFormData.customer_id);
        formData.append('items', JSON.stringify(tradeInFormData.items));

        fetch(`${urlRoot}/tradeins/add`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `${urlRoot}/tradeins/show/${data.trade_in_id}`;
            } else {
                alert('Erro ao registrar o trade-in: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocorreu um erro de comunicação.');
        });
    });
});

// Script para o formulário de empréstimo
document.addEventListener('DOMContentLoaded', function() {
    const loanForm = document.getElementById('loan-form');
    if (!loanForm) {
        return;
    }

    const urlRoot = loanForm.dataset.urlRoot;
    const addLoanItemBtn = document.getElementById('add-loan-item-btn');
    const loanItemsTableBody = document.querySelector('#loan-items-table tbody');

    let loanItems = [];

    // Adicionar item de empréstimo
    addLoanItemBtn.addEventListener('click', function() {
        const stockItemSearch = document.getElementById('stock_item_search');
        const selectedOption = stockItemSearch.options[stockItemSearch.selectedIndex];
        
        if (!selectedOption.value) {
            alert('Por favor, selecione um item de estoque.');
            return;
        }

        const stockItemId = selectedOption.value;
        const productDisplayName = selectedOption.textContent;
        const productId = selectedOption.dataset.productId;
        const estadoSaida = document.getElementById('estado_saida').value;

        // Verifica se o item já foi adicionado
        if (loanItems.find(item => item.stock_item_id === stockItemId)) {
            alert('Este item já foi adicionado ao empréstimo.');
            return;
        }

        const item = {
            stock_item_id: stockItemId,
            product_id: productId,
            product_display_name: productDisplayName,
            estado_saida: estadoSaida
        };

        loanItems.push(item);
        renderLoanItemsTable();
        clearLoanItemForm();
    });

    // Renderiza a tabela de itens de empréstimo
    function renderLoanItemsTable() {
        loanItemsTableBody.innerHTML = '';
        loanItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.product_display_name}</td>
                <td>${item.estado_saida || 'N/A'}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-loan-item" data-index="${index}">&times;</button></td>
            `;
            loanItemsTableBody.appendChild(row);
        });
    }

    // Limpa o formulário de item de empréstimo
    function clearLoanItemForm() {
        document.getElementById('stock_item_search').value = '';
        document.getElementById('estado_saida').value = '';
    }

    // Remover item de empréstimo
    loanItemsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-loan-item')) {
            const index = e.target.dataset.index;
            loanItems.splice(index, 1);
            renderLoanItemsTable();
        }
    });

    // Submeter o formulário de empréstimo
    loanForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const loanFormData = {
            customer_id: document.getElementById('customer_id').value,
            data_saida: document.getElementById('data_saida').value,
            data_prevista_retorno: document.getElementById('data_prevista_retorno').value,
            observacoes: document.getElementById('observacoes').value,
            items: loanItems
        };

        if (!loanFormData.customer_id || !loanFormData.data_saida || loanItems.length === 0) {
            alert('Por favor, preencha o cliente, a data de saída e adicione pelo menos um item.');
            return;
        }

        const formData = new FormData();
        for (const key in loanFormData) {
            if (key === 'items') {
                formData.append(key, JSON.stringify(loanFormData[key]));
            } else {
                formData.append(key, loanFormData[key]);
            }
        }

        fetch(`${urlRoot}/loans/add`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `${urlRoot}/loans/show/${data.loan_id}`;
            } else {
                alert('Erro ao registrar o empréstimo: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocorreu um erro de comunicação.');
        });
    });
});