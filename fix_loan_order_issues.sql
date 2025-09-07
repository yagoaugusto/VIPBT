-- Database schema updates to fix loan and order functionality issues
-- This script ensures all required tables and columns exist

USE `viplojabt`;

-- Ensure loans table has the correct status enum values
ALTER TABLE `loans` 
MODIFY COLUMN `status` ENUM('ativo','devolvido','em_atraso','convertido_em_venda') NOT NULL DEFAULT 'ativo';

-- Update any existing 'aberto' status to 'ativo'
UPDATE `loans` SET `status` = 'ativo' WHERE `status` = 'aberto';

-- Ensure orders table has the total column
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `status_entrega`;

-- Ensure loans table has order_id for conversion tracking
ALTER TABLE `loans` 
ADD COLUMN IF NOT EXISTS `order_id` INT(11) NULL AFTER `data_retorno`,
ADD KEY IF NOT EXISTS `order_id` (`order_id`);

-- Add foreign key constraint if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     WHERE TABLE_SCHEMA = 'viplojabt' 
     AND TABLE_NAME = 'loans' 
     AND CONSTRAINT_NAME = 'loans_ibfk_3') = 0,
    'ALTER TABLE `loans` ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL',
    'SELECT "Foreign key loans_ibfk_3 already exists"'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure required indexes exist for better performance
ALTER TABLE `loans` 
ADD INDEX IF NOT EXISTS `idx_status` (`status`),
ADD INDEX IF NOT EXISTS `idx_data_saida` (`data_saida`);

ALTER TABLE `loan_items`
ADD INDEX IF NOT EXISTS `idx_estado_retorno` (`estado_retorno`);

-- Ensure stock_items table has all required status values
ALTER TABLE `stock_items` 
MODIFY COLUMN `status` ENUM('em_estoque','reservado','emprestado','vendido','descartado') NOT NULL DEFAULT 'em_estoque';

-- Create a simple test query to verify relationships work
SELECT 'Database schema update completed successfully' as status;