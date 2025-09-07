# Correções para Erros de Empréstimo e Pedidos - VIPBT

## Problemas Resolvidos

### 1. Erro em VIPLOJABT/loans/show/1
**Erro:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.preco' in 'field list'`

**Causa:** O código estava tentando acessar a coluna `preco` diretamente da tabela `products`, mas os preços são armazenados na tabela `product_prices`.

**Solução:** Atualizado o query SQL em `models/LoanModel.php` para fazer o JOIN correto com a tabela `product_prices`:

```sql
-- ANTES (incorreto):
SELECT p.id, p.preco 
FROM products p 
JOIN stock_items si ON p.id = si.product_id 
WHERE si.id = :stock_item_id

-- DEPOIS (correto):
SELECT p.id, 
       COALESCE(pp.preco, 0) as preco
FROM products p 
JOIN stock_items si ON p.id = si.product_id 
LEFT JOIN product_prices pp ON pp.product_id = p.id 
    AND pp.vigente_desde = (SELECT MAX(vigente_desde) FROM product_prices WHERE product_id = p.id)
WHERE si.id = :stock_item_id
```

### 2. Erro em VIPLOJABT/orders/show/5
**Erro:** `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'viplojabt.fulfillments' doesn't exist`

**Causa:** A tabela `fulfillments` não existe no banco de dados, mas é referenciada pelo código em `models/OrderModel.php`.

**Solução:** Adicionada a criação da tabela `fulfillments` nos scripts de atualização do banco de dados.

## Arquivos Modificados

### 1. `models/LoanModel.php`
- Corrigido o query SQL na linha 213-221 para usar JOIN com `product_prices`
- Adicionado `COALESCE(pp.preco, 0)` para tratar casos onde não há preço definido

### 2. `fix_loan_order_issues.sql`
- Adicionada criação da tabela `fulfillments` se não existir

### 3. `fix_loan_order_database_issues.sql` (novo arquivo)
- Script completo para corrigir ambos os problemas
- Inclui criação das tabelas `product_prices` e `fulfillments`
- Adiciona preços padrão para produtos sem preço

## Como Aplicar as Correções

### 1. Atualização do Banco de Dados
Execute o script SQL no banco de dados:

```bash
mysql -u root -p viplojabt < fix_loan_order_database_issues.sql
```

### 2. Verificação das Correções
Após executar o script, teste:

1. Acesse `VIPLOJABT/loans/show/1` - o erro de coluna não encontrada deve estar resolvido
2. Acesse `VIPLOJABT/orders/show/5` - o erro de tabela não encontrada deve estar resolvido

## Estrutura das Tabelas Criadas/Verificadas

### Tabela `product_prices`
```sql
CREATE TABLE `product_prices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `custo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `preco` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `vigente_desde` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);
```

### Tabela `fulfillments`
```sql
CREATE TABLE `fulfillments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `status` ENUM('preparando','enviado','entregue') NOT NULL DEFAULT 'preparando',
  `transportadora` VARCHAR(255) NULL,
  `codigo_rastreio` VARCHAR(255) NULL,
  `enviado_em` DATETIME NULL,
  `entregue_em` DATETIME NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `fulfillments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
);
```

## Notas Técnicas

1. **Performance:** O novo query usa LEFT JOIN para garantir que produtos sem preço ainda sejam retornados
2. **Segurança:** Mantido o uso de prepared statements com bind parameters
3. **Compatibilidade:** As mudanças são retrocompatíveis e não afetam funcionalidades existentes
4. **Defaults:** Produtos sem preço retornam 0.00 através do COALESCE

## Monitoramento

Após aplicar as correções, monitore:
- Logs de erro para confirmar que os problemas foram resolvidos
- Performance dos queries modificados
- Funcionalidade de conversão de empréstimos em vendas
- Gestão de expedição de pedidos