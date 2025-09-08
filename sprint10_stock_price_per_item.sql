-- sprint10_stock_price_per_item.sql
-- Adiciona a coluna preco_venda em stock_items para permitir preco por item de estoque

ALTER TABLE stock_items
ADD COLUMN IF NOT EXISTS preco_venda DECIMAL(10,2) NULL AFTER aquisicao_custo;

-- Índice opcional para consultas por preco_venda
-- CREATE INDEX idx_stock_items_preco_venda ON stock_items(preco_venda);
