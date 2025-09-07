-- Improve Order Sales Tracking
-- Add new status to track order conversion to sales

USE `viplojabt`;

-- Update orders table to include 'vendido' status
ALTER TABLE `orders` 
MODIFY COLUMN `status_pedido` ENUM('novo','confirmado','vendido','cancelado') NOT NULL DEFAULT 'novo';

-- Add conversion tracking field
ALTER TABLE `orders` 
ADD COLUMN `data_confirmacao_venda` TIMESTAMP NULL COMMENT 'Data quando o pedido foi confirmado como venda',
ADD COLUMN `confirmado_por` INT(11) NULL COMMENT 'ID do usuário que confirmou a venda',
ADD INDEX `idx_status_pedido` (`status_pedido`),
ADD INDEX `idx_data_confirmacao` (`data_confirmacao_venda`);

-- Add foreign key for confirmado_por if users table exists
-- This will be added conditionally
SET @fk_exists = (SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'viplojabt' 
    AND TABLE_NAME = 'orders' 
    AND CONSTRAINT_NAME = 'orders_confirmado_por_fk');

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE orders ADD CONSTRAINT orders_confirmado_por_fk FOREIGN KEY (confirmado_por) REFERENCES users(id)',
    'SELECT "Foreign key already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;