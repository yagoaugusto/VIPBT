# Solução Completa para Erro SQLSTATE[HY093] na Criação de Pedidos

## Problema
Quando tentamos criar um pedido em `VIPLOJABT/orders/add`, ocorre o erro:
```
Erro ao criar o pedido: SQLSTATE[HY093]: Invalid parameter number
```

## Causa Raiz
O erro é causado por incompatibilidade entre a estrutura do banco de dados e as consultas SQL. Especificamente:

1. A tabela `order_credits` foi criada originalmente no Sprint 5 sem a coluna `trade_in_id`
2. No Sprint 8, a coluna `trade_in_id` foi adicionada via script de migração
3. Alguns ambientes podem não ter executado todas as migrações
4. O código tentava inserir dados com um número diferente de parâmetros SQL dependendo da estrutura do banco

## Solução Implementada

### 1. Detecção Dinâmica do Schema
Implementamos métodos que verificam a estrutura atual do banco antes de executar SQL:

```php
private function orderCreditsHasTradeInColumn(){
    $this->db->query("
        SELECT COUNT(*) as column_exists 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'order_credits' 
        AND COLUMN_NAME = 'trade_in_id'
    ");
    $result = $this->db->single();
    return $result && $result->column_exists > 0;
}
```

### 2. SQL Condicional
O método `insertOrderCredit` usa SQL diferentes baseado na estrutura do banco:

```php
private function insertOrderCredit($order_id, $origem, $descricao, $valor, $trade_in_id = null){
    $hasTradeInColumn = $this->orderCreditsHasTradeInColumn();
    
    if ($hasTradeInColumn) {
        // Schema novo com coluna trade_in_id
        $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id) VALUES (:order_id, :origem, :descricao, :valor, :trade_in_id)");
        // ... bind todos os 5 parâmetros
    } else {
        // Schema antigo sem coluna trade_in_id
        $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, :origem, :descricao, :valor)");
        // ... bind apenas 4 parâmetros
    }
}
```

### 3. Compatibilidade Retroativa
- Funciona com bancos que têm ou não têm a coluna `trade_in_id`
- Log de avisos quando usa modo de compatibilidade
- Não quebra funcionalidades existentes

## Como Aplicar a Solução

### Passo 1: Atualizar Estrutura do Banco
Execute o script de atualização do banco:

```bash
mysql -u root -p viplojabt < database_structure_fix.sql
```

Este script:
- ✅ Verifica se a tabela `order_credits` existe
- ✅ Adiciona a coluna `trade_in_id` se não existir
- ✅ Adiciona índices e chaves estrangeiras necessárias
- ✅ Adiciona a coluna `total` na tabela `orders` se necessário
- ✅ É seguro executar múltiplas vezes

### Passo 2: Validar a Implementação
Execute o script de validação:

```bash
php validate_comprehensive_fix.php
```

### Passo 3: Testar no Sistema
1. Acesse `/orders/add`
2. Crie um pedido com itens normais
3. Se aplicável, teste com créditos de trade-in
4. Verifique os logs de erro

## Arquivos Modificados/Criados

### Arquivos Principais do Sistema (já implementados)
- `models/OrderModel.php` - Método `insertOrderCredit` com detecção de schema
- `models/TradeInModel.php` - Método `addOrderCredit` com detecção de schema
- `controllers/Orders.php` - Tratamento de erros melhorado

### Scripts de Solução (novos)
- `database_structure_fix.sql` - Script completo de atualização do banco
- `validate_comprehensive_fix.php` - Script de validação da implementação
- `test_order_creation_fix.php` - Script de teste das consultas SQL
- `SOLUTION_README.md` - Esta documentação

## Estrutura Final das Tabelas

### Tabela `order_credits` (atualizada)
```sql
CREATE TABLE `order_credits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `origem` ENUM('trade_in','ajuste') NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `trade_in_id` INT(11) NULL,  -- Nova coluna adicionada
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `trade_in_id` (`trade_in_id`),
  CONSTRAINT `order_credits_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_credits_ibfk_2` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE SET NULL
);
```

### Tabela `orders` (verificada)
Garantimos que tem a coluna `total`:
```sql
ALTER TABLE `orders` ADD COLUMN `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00;
```

## Benefícios da Solução

1. **Elimina SQLSTATE[HY093]**: Número de parâmetros sempre consistente
2. **Compatibilidade Total**: Funciona em qualquer versão do banco
3. **Fácil Manutenção**: Código auto-adaptável
4. **Segurança**: Mantém prepared statements
5. **Monitoramento**: Logs informativos para debugging
6. **Robustez**: Tratamento de erros abrangente

## Monitoramento Pós-Implementação

Após aplicar a solução, monitore:

1. **Logs de Erro**: Verifique se ainda há erros SQLSTATE[HY093]
2. **Logs de Aplicação**: Procure por mensagens sobre "fallback mode"
3. **Funcionalidade**: Teste criação de pedidos regularmente
4. **Performance**: Monitore se a detecção de schema impacta performance

## Troubleshooting

### Se ainda há erros SQLSTATE[HY093]:
1. Verifique se o script `database_structure_fix.sql` foi executado
2. Execute `validate_comprehensive_fix.php` para análise detalhada
3. Verifique logs da aplicação para detalhes específicos
4. Confirme que todos os ambientes têm a mesma estrutura de banco

### Se há problemas de performance:
- A detecção de schema é executada a cada inserção
- Considere implementar cache do resultado da detecção
- Em produção, todos os ambientes devem ter a mesma estrutura

## Conclusão

Esta solução resolve definitivamente o erro SQLSTATE[HY093] implementando:
- ✅ Detecção automática da estrutura do banco
- ✅ SQL condicional baseado no schema disponível  
- ✅ Compatibilidade total com versões antigas e novas
- ✅ Tratamento robusto de erros
- ✅ Scripts de validação e teste

A implementação garante que o sistema funcione independentemente da versão do banco de dados, eliminando erros de incompatibilidade de parâmetros SQL.