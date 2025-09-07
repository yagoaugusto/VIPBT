USE `viplojabt`;

-- Add trade_in_id column to order_credits table
ALTER TABLE `order_credits` 
ADD COLUMN `trade_in_id` INT(11) NULL AFTER `valor`,
ADD KEY `trade_in_id` (`trade_in_id`),
ADD CONSTRAINT `order_credits_ibfk_2` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE SET NULL;

-- Add total column to orders table
ALTER TABLE `orders` 
ADD COLUMN `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `status_entrega`;

-- Add order_id column to loans table for conversion tracking
ALTER TABLE `loans` 
ADD COLUMN `order_id` INT(11) NULL AFTER `data_retorno`,
ADD KEY `order_id` (`order_id`),
ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;