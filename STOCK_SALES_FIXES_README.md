# Correções do Sistema de Estoque e Vendas - VIPBT

## Problemas Corrigidos

### 1. Vendas não funcionavam bem e não tinham relação com estoque
**Problema:** O sistema de vendas estava usando um caminho relativo incorreto para incluir o StockModel e não validava disponibilidade antes de processar vendas.

**Solução:** 
- Corrigido o caminho de include em `OrderModel.php` (linha 81): `require_once '../models/StockModel.php'` → `require_once __DIR__ . '/StockModel.php'`
- Adicionada validação de disponibilidade de estoque antes de processar itens da venda
- Implementado controle de transações com rollback em caso de estoque insuficiente

### 2. Entrada de itens no estoque não funcionava bem
**Problema:** Falta de validação adequada e tratamento de erros na entrada de estoque.

**Solução:**
- Adicionada validação completa de dados de entrada (product_id, quantidade, custo)
- Verificação se o produto existe antes de processar
- Melhor tratamento de exceções com mensagens específicas
- Garantia de que itens novos são criados com status 'em_estoque'

## Arquivos Modificados

### 1. `models/OrderModel.php`
- **Linha 81:** Corrigido caminho de include do StockModel
- **Linhas 87-95:** Adicionada validação de estoque antes de processar venda
- **Linha 128:** Melhorado tratamento de exceções

### 2. `models/StockModel.php` 
- **Linhas 29-51:** Adicionada validação completa de entrada de estoque
- **Linhas 160-179:** Novo método `checkMultipleProductsAvailability()` para verificar disponibilidade de múltiplos produtos
- **Linha 48:** Garantido status 'em_estoque' para novos itens
- **Linha 155:** Melhorado log de erros

### 3. `controllers/Stock.php`
- **Linhas 62-75:** Implementado try-catch para capturar erros do modelo
- **Linhas 104-126:** Novo endpoint `checkAvailability()` para verificação via API
- **Linha 47:** Adicionado campo `general_err` para exibir erros gerais

### 4. `controllers/Orders.php`
- **Linhas 49-56:** Implementado try-catch para capturar erros de estoque insuficiente

### 5. `views/stock/add.php`
- **Linhas 6-10:** Adicionado display de erros gerais

### 6. `fix_stock_sales_issues.sql` (NOVO)
- Script SQL para garantir consistência do banco de dados
- Atualização de status padrão para stock_items
- Criação de índices para melhor performance
- Verificação da estrutura das tabelas

## Como Aplicar as Correções

### 1. Aplicar as mudanças no código
As mudanças no código já foram aplicadas nos arquivos PHP.

### 2. Executar as atualizações SQL
```bash
mysql -u [usuario] -p[senha] viplojabt < fix_stock_sales_issues.sql
```

### 3. Testar as funcionalidades
1. **Teste de Entrada de Estoque:**
   - Acesse `/stock/add`
   - Tente adicionar produto sem selecionar: deve mostrar erro específico
   - Tente adicionar quantidade inválida: deve mostrar erro específico
   - Adicione entrada válida: deve registrar com sucesso

2. **Teste de Venda:**
   - Acesse `/orders/add` 
   - Tente vender quantidade maior que disponível: deve mostrar erro de estoque insuficiente
   - Venda dentro do disponível: deve processar normalmente e reduzir estoque

3. **Verificação de Saldos:**
   - Acesse `/stock/balances`
   - Verifique se os saldos calculados estão corretos após vendas

## Novos Recursos

### 1. API de Verificação de Estoque
**Endpoint:** `POST /stock/checkAvailability`

**Exemplo de uso:**
```javascript
fetch('/stock/checkAvailability', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        items: [
            {id: 1, qtd: 5},
            {id: 2, qtd: 3}
        ]
    })
}).then(response => response.json())
.then(data => {
    if(data.success) {
        data.availability.forEach(item => {
            console.log(`Produto ${item.product_id}: ${item.available} disponível, ${item.requested} solicitado, ${item.sufficient ? 'OK' : 'INSUFICIENTE'}`);
        });
    }
});
```

### 2. Validação Aprimorada
- Validação de dados antes de processamento
- Mensagens de erro específicas e amigáveis
- Controle de transações com rollback automático

### 3. Logs de Erro
- Erros são logados automaticamente para debug
- Mensagens específicas para diferentes tipos de erro

## Melhorias de Performance

1. **Índices adicionados:**
   - `idx_stock_items_product_status` para consultas de estoque
   - `idx_inventory_moves_product_tipo` para cálculos de saldo

2. **Consultas otimizadas:**
   - Verificação de estoque antes de processar vendas
   - Uso adequado de transações

## Próximos Passos Recomendados

1. **Testes em ambiente de produção**
2. **Monitoramento de logs** para identificar possíveis problemas
3. **Backup regular** dos dados antes de mudanças importantes
4. **Considerar implementar cache** para consultas de saldo frequentes
5. **Adicionar testes automatizados** para prevenir regressões futuras