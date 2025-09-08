-- Migration: add data_cobranca to receivables and index it
USE `viplojabt`;

-- Add column if not exists (MySQL 8.0+ workaround via INFORMATION_SCHEMA)
SET @col_exists = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'receivables' AND COLUMN_NAME = 'data_cobranca'
);

SET @sql = CASE 
  WHEN @col_exists = 0 THEN 'ALTER TABLE `receivables` ADD COLUMN `data_cobranca` DATE NULL AFTER `valor_a_receber`'
  ELSE 'SELECT "Column data_cobranca already exists in receivables" as status'
END;

PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create index if not exists
SET @idx_exists = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'receivables' AND INDEX_NAME = 'idx_receivables_data_cobranca'
);

SET @sql = CASE 
  WHEN @idx_exists = 0 THEN 'CREATE INDEX `idx_receivables_data_cobranca` ON `receivables` (`data_cobranca`)'
  ELSE 'SELECT "Index idx_receivables_data_cobranca already exists" as status'
END;

PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Done
SELECT 'fix_receivables_charge_date.sql completed' as status;
