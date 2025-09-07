-- Database Updates for Stock Management Improvements
-- Run this script to ensure all stock management features work correctly

USE `viplojabt`;

-- Ensure inventory_moves table has all necessary columns
-- This is likely already created by sprint2.sql, but we verify the structure

-- Check if inventory_moves table exists and has correct structure
-- If it doesn't exist, create it
CREATE TABLE IF NOT EXISTS `inventory_moves` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `stock_item_id` INT(11) NULL,
  `tipo` ENUM('entrada','saida','ajuste','reserva','baixa_reserva','emprestimo_saida','emprestimo_retorno') NOT NULL,
  `qtd` INT(11) NOT NULL,
  `ref_origem` VARCHAR(255) NULL,
  `id_origem` INT(11) NULL,
  `observacao` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `stock_item_id` (`stock_item_id`),
  CONSTRAINT `inventory_moves_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `inventory_moves_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure stock_items table exists with correct structure
CREATE TABLE IF NOT EXISTS `stock_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `condicao` ENUM('novo','seminovo') NOT NULL,
  `grade` ENUM('A','B','C') NULL,
  `serie` VARCHAR(255) NULL,
  `aquisicao_tipo` ENUM('compra','trade_in','ajuste','retorno_emprestimo') NOT NULL,
  `aquisicao_custo` DECIMAL(10,2) NULL,
  `status` ENUM('em_estoque','reservado','emprestado','vendido','descartado') NOT NULL DEFAULT 'em_estoque',
  `local` VARCHAR(255) NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `stock_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance on stock balance queries
CREATE INDEX IF NOT EXISTS `idx_inventory_moves_product_tipo` ON `inventory_moves` (`product_id`, `tipo`);
CREATE INDEX IF NOT EXISTS `idx_stock_items_product_status` ON `stock_items` (`product_id`, `status`);

-- Sample data - Add some test products if they don't exist (optional)
-- This helps with testing the stock functionality
INSERT IGNORE INTO `products` (`id`, `nome`, `sku`, `descricao`, `brand_id`, `category_id`, `tipo_condicao`) 
SELECT 1, 'Raquete Teste BT01', 'BT001', 'Raquete para teste do sistema', 1, 1, 'novo'
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `sku` = 'BT001')
AND EXISTS (SELECT 1 FROM `brands` WHERE `id` = 1)
AND EXISTS (SELECT 1 FROM `categories` WHERE `id` = 1);

INSERT IGNORE INTO `products` (`id`, `nome`, `sku`, `descricao`, `brand_id`, `category_id`, `tipo_condicao`) 
SELECT 2, 'Raquete Teste BT02', 'BT002', 'Segunda raquete para teste', 1, 1, 'novo'
WHERE NOT EXISTS (SELECT 1 FROM `products` WHERE `sku` = 'BT002')
AND EXISTS (SELECT 1 FROM `brands` WHERE `id` = 1)
AND EXISTS (SELECT 1 FROM `categories` WHERE `id` = 1);

-- Verification queries (commented out, uncomment to check)
-- SELECT 'inventory_moves table structure:' as info;
-- DESCRIBE inventory_moves;
-- SELECT 'stock_items table structure:' as info;
-- DESCRIBE stock_items;
-- SELECT 'Stock balance calculation test:' as info;
-- SELECT p.nome, 
--        COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('entrada', 'emprestimo_retorno')), 0) as entradas,
--        COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('saida', 'emprestimo_saida')), 0) as saidas,
--        COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('entrada', 'emprestimo_retorno')), 0) - 
--        COALESCE((SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('saida', 'emprestimo_saida')), 0) as saldo
-- FROM products p LIMIT 5;

COMMIT;