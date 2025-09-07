# Sistema de Rastreamento de Vendas - VIPBT

## Problema Resolvido

O sistema anterior não diferenciava pedidos de vendas efetivadas. Todos os pedidos eram tratados igualmente, impossibilitando análises de conversão e relatórios sobre:
- Quantos pedidos se transformaram em vendas
- Taxa de conversão por canal de venda
- Performance de vendedores
- Análise de conversão por período

## Solução Implementada

### 1. **Novos Status de Pedido**

O campo `status_pedido` foi expandido para incluir:

- **`novo`**: Pedido recém-criado (status inicial)
- **`confirmado`**: Pedido confirmado pelo cliente/vendedor
- **`vendido`**: Pedido efetivamente convertido em venda ✨
- **`cancelado`**: Pedido cancelado

### 2. **Campos Adicionais para Rastreamento**

```sql
-- Novos campos na tabela orders
data_confirmacao_venda TIMESTAMP NULL  -- Quando foi confirmado como venda
confirmado_por INT(11) NULL             -- Quem confirmou a venda
```

### 3. **Novos Métodos na OrderModel**

#### `confirmOrderAsSale($order_id, $confirmed_by_user_id)`
- Converte um pedido em venda confirmada
- Registra timestamp e usuário responsável
- Só permite conversão de pedidos com status 'novo' ou 'confirmado'

#### `updateOrderStatus($order_id, $status, $confirmed_by_user_id)`
- Atualiza status do pedido com validação
- Registra dados de confirmação quando status = 'vendido'

#### `getOrderConversionStats($start_date, $end_date, $channel_id, $seller_id)`
- Estatísticas gerais de conversão
- Filtros por período, canal e vendedor
- Retorna taxa de conversão percentual

#### `getConversionStatsByChannel($start_date, $end_date)`
- Performance detalhada por canal de venda
- Total de pedidos vs vendas confirmadas
- Valor total de vendas por canal

#### `getConversionStatsBySeller($start_date, $end_date)`
- Performance detalhada por vendedor
- Estatísticas de conversão individuais
- Valor total de vendas por vendedor

### 4. **Interface Atualizada**

#### Lista de Pedidos (`/orders`)
- Badges coloridos por status:
  - 🔘 Novo (cinza)
  - 🔵 Confirmado (azul)
  - ✅ Vendido (verde)
  - ❌ Cancelado (vermelho)
- Link para "Estatísticas de Conversão"

#### Detalhes do Pedido (`/orders/show/{id}`)
- Botões de ação baseados no status atual:
  - **Pedido Novo**: "Confirmar Pedido" + "Confirmar Venda" + "Cancelar"
  - **Pedido Confirmado**: "Confirmar Venda" + "Cancelar"
  - **Venda Confirmada**: Mostra data/hora da confirmação
- Interface responsiva com confirmações JavaScript

#### Página de Estatísticas (`/orders/conversionStats`)
- Filtros por período, canal e vendedor
- Resumo geral com taxa de conversão
- Tabelas detalhadas por canal e vendedor
- Gráficos de progresso visuais

### 5. **Integração com Conversão de Empréstimos**

O `LoanModel` foi atualizado para criar pedidos já com status `vendido` quando converte empréstimos, pois estas são vendas imediatas.

## Como Usar

### 1. **Aplicar Mudanças no Banco de Dados**

Execute o script SQL:
```bash
mysql -u usuario -p viplojabt < improve_order_sales_tracking.sql
```

### 2. **Fluxo de Trabalho Atualizado**

1. **Criar Pedido**: Status inicial = 'novo'
2. **Confirmar Pedido**: Status = 'confirmado' (opcional)
3. **Confirmar Venda**: Status = 'vendido' (✨ objetivo principal)
4. **Analisar Performance**: Acessar `/orders/conversionStats`

### 3. **Endpoints da API**

```php
// Confirmar pedido como venda
POST /orders/confirmSale/{order_id}

// Atualizar status do pedido
POST /orders/updateOrderStatus/{order_id}
Body: {"status": "vendido"}

// Obter estatísticas (JSON)
GET /orders/conversionStats?start_date=2024-01-01&channel_id=1
Header: X-Requested-With: XMLHttpRequest
```

## Benefícios Implementados

### 1. **Rastreamento Preciso**
- Distinção clara entre pedidos e vendas
- Timestamps de confirmação
- Auditoria de quem confirmou vendas

### 2. **Relatórios e Análises**
- Taxa de conversão geral e por filtros
- Performance por canal de venda
- Performance por vendedor
- Análise temporal de conversões

### 3. **Facilita Decisões de Negócio**
- Identificar canais mais efetivos
- Avaliar performance de vendedores
- Otimizar processos de conversão
- Acompanhar tendências temporais

### 4. **Base para Futuras Melhorias**
- Sistema preparado para relatórios avançados
- Integração com dashboards
- Análises de funil de vendas
- Automações baseadas em conversão

## Compatibilidade

✅ **Totalmente compatível** com o sistema existente:
- Status 'faturado' mantido para compatibilidade
- Funcionalidades existentes preservadas
- Migração suave de dados

## Próximos Passos Sugeridos

1. **Dashboard Visual**: Gráficos interativos de conversão
2. **Alertas Automáticos**: Notificações de baixa conversão
3. **Metas de Conversão**: Definir targets por vendedor/canal
4. **Relatórios Agendados**: Envio automático de estatísticas
5. **API REST**: Endpoints para integrações externas

---

**Versão**: 1.0  
**Data**: Setembro 2024  
**Compatibilidade**: VIPBT v2.0+