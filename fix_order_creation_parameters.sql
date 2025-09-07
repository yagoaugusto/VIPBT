-- Fix Order Creation Parameter Issues
-- Ensure order_credits table has the trade_in_id column

USE `viplojabt`;

-- Add trade_in_id column to order_credits table if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = 'viplojabt' 
     AND TABLE_NAME = 'order_credits' 
     AND COLUMN_NAME = 'trade_in_id') = 0,
    'ALTER TABLE `order_credits` ADD COLUMN `trade_in_id` INT(11) NULL AFTER `valor`, ADD KEY `trade_in_id` (`trade_in_id`)',
    'SELECT "Column trade_in_id already exists in order_credits table"'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     WHERE TABLE_SCHEMA = 'viplojabt' 
     AND TABLE_NAME = 'order_credits' 
     AND CONSTRAINT_NAME = 'order_credits_ibfk_2') = 0,
    'ALTER TABLE `order_credits` ADD CONSTRAINT `order_credits_ibfk_2` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE SET NULL',
    'SELECT "Foreign key order_credits_ibfk_2 already exists"'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure orders table has the total column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = 'viplojabt' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'total') = 0,
    'ALTER TABLE `orders` ADD COLUMN `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `status_entrega`',
    'SELECT "Column total already exists in orders table"'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Order creation parameter fix completed successfully' as status;