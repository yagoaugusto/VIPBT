-- Fix for Stock and Sales Issues
-- Run this to ensure database consistency for improved stock management

USE `viplojabt`;

-- Ensure stock_items have proper default status
UPDATE stock_items SET status = 'em_estoque' WHERE status IS NULL OR status = '';

-- Add index for better performance on stock operations
CREATE INDEX IF NOT EXISTS idx_stock_items_product_status ON stock_items (product_id, status);
CREATE INDEX IF NOT EXISTS idx_inventory_moves_product_tipo ON inventory_moves (product_id, tipo);

-- Verify stock_items table has all required columns and proper structure
-- This ensures compatibility with the updated StockModel
ALTER TABLE stock_items 
MODIFY COLUMN status ENUM('em_estoque','reservado','emprestado','vendido','descartado') NOT NULL DEFAULT 'em_estoque';

-- Ensure inventory_moves table supports all the movement types used in the system
ALTER TABLE inventory_moves 
MODIFY COLUMN tipo ENUM('entrada','saida','ajuste','reserva','baixa_reserva','emprestimo_saida','emprestimo_retorno') NOT NULL;

-- Add observacao field to inventory_moves if it doesn't exist (should already exist but ensuring)
ALTER TABLE inventory_moves 
ADD COLUMN IF NOT EXISTS observacao TEXT NULL;

-- Verification query to check stock balance consistency
-- Uncomment and run manually to verify after applying changes:
-- SELECT 
--     p.id,
--     p.nome,
--     p.sku,
--     COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('entrada', 'emprestimo_retorno')), 0) as total_entradas,
--     COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('saida', 'emprestimo_saida', 'baixa_reserva')), 0) as total_saidas,
--     COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('entrada', 'emprestimo_retorno')), 0) - 
--     COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('saida', 'emprestimo_saida', 'baixa_reserva')), 0) as saldo_calculado,
--     COUNT(si.id) as itens_fisicos_total,
--     SUM(CASE WHEN si.status = 'em_estoque' THEN 1 ELSE 0 END) as itens_disponiveis
-- FROM products p
-- LEFT JOIN stock_items si ON p.id = si.product_id
-- GROUP BY p.id, p.nome, p.sku
-- HAVING saldo_calculado != 0 OR itens_fisicos_total > 0
-- ORDER BY p.nome;

COMMIT;