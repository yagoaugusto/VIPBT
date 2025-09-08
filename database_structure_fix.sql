-- Comprehensive Database Update for Order Creation Parameter Fix
-- This script ensures the database schema is correct for order creation

USE `viplojabt`;

-- ========================================
-- PART 1: Check and Update order_credits table
-- ========================================

-- Check if order_credits table exists
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                     WHERE TABLE_SCHEMA = 'viplojabt' AND TABLE_NAME = 'order_credits');

-- Create order_credits table if it doesn't exist
SET @sql = CASE 
    WHEN @table_exists = 0 THEN 
        'CREATE TABLE `order_credits` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `order_id` INT(11) NOT NULL,
            `origem` ENUM(''trade_in'',''ajuste'') NOT NULL,
            `descricao` VARCHAR(255) NULL,
            `valor` DECIMAL(10,2) NOT NULL,
            `trade_in_id` INT(11) NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `order_id` (`order_id`),
            KEY `trade_in_id` (`trade_in_id`),
            CONSTRAINT `order_credits_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    ELSE 
        'SELECT "Table order_credits already exists" as status'
END;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add trade_in_id column if it doesn't exist (for existing tables)
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                      WHERE TABLE_SCHEMA = 'viplojabt' 
                      AND TABLE_NAME = 'order_credits' 
                      AND COLUMN_NAME = 'trade_in_id');

SET @sql = CASE 
    WHEN @column_exists = 0 THEN 
        'ALTER TABLE `order_credits` ADD COLUMN `trade_in_id` INT(11) NULL AFTER `valor`'
    ELSE 
        'SELECT "Column trade_in_id already exists in order_credits" as status'
END;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for trade_in_id if it doesn't exist
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = 'viplojabt'
                     AND TABLE_NAME = 'order_credits'
                     AND INDEX_NAME = 'trade_in_id');

SET @sql = CASE 
    WHEN @index_exists = 0 THEN 
        'ALTER TABLE `order_credits` ADD KEY `trade_in_id` (`trade_in_id`)'
    ELSE 
        'SELECT "Index trade_in_id already exists" as status'
END;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint for trade_in_id if it doesn't exist
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                  WHERE TABLE_SCHEMA = 'viplojabt' 
                  AND TABLE_NAME = 'order_credits' 
                  AND CONSTRAINT_NAME = 'order_credits_ibfk_2');

-- Check if trade_ins table exists before adding foreign key
SET @trade_ins_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                         WHERE TABLE_SCHEMA = 'viplojabt' AND TABLE_NAME = 'trade_ins');

SET @sql = CASE 
    WHEN @fk_exists = 0 AND @trade_ins_exists = 1 THEN 
        'ALTER TABLE `order_credits` ADD CONSTRAINT `order_credits_ibfk_2` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE SET NULL'
    WHEN @fk_exists > 0 THEN
        'SELECT "Foreign key order_credits_ibfk_2 already exists" as status'
    ELSE 
        'SELECT "Cannot add foreign key: trade_ins table does not exist" as status'
END;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- PART 2: Check and Update orders table
-- ========================================

-- Ensure orders table has the total column
SET @total_column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE TABLE_SCHEMA = 'viplojabt' 
                            AND TABLE_NAME = 'orders' 
                            AND COLUMN_NAME = 'total');

SET @sql = CASE 
    WHEN @total_column_exists = 0 THEN 
        'ALTER TABLE `orders` ADD COLUMN `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `observacao`'
    ELSE 
        'SELECT "Column total already exists in orders table" as status'
END;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- PART 3: Validation and Results
-- ========================================

-- Show final table structure
SELECT 'Final order_credits table structure:' as info;
DESCRIBE order_credits;

SELECT 'Final orders table relevant columns:' as info;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'viplojabt' 
AND TABLE_NAME = 'orders' 
AND COLUMN_NAME IN ('total', 'observacao', 'created_at')
ORDER BY ORDINAL_POSITION;

-- Check foreign keys
SELECT 'Foreign key constraints on order_credits:' as info;
SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'viplojabt' 
AND TABLE_NAME = 'order_credits' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

SELECT 'Database update completed successfully!' as status;