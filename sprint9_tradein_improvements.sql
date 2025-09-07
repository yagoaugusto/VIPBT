USE `viplojabt`;

-- Add observacoes_aprovacao column to trade_ins table
ALTER TABLE `trade_ins` 
ADD COLUMN `observacoes_aprovacao` TEXT NULL AFTER `avaliador_user_id`;

-- Add aprovado_por_user_id to track who approved the trade-in
ALTER TABLE `trade_ins` 
ADD COLUMN `aprovado_por_user_id` INT(11) NULL AFTER `observacoes_aprovacao`,
ADD KEY `aprovado_por_user_id` (`aprovado_por_user_id`),
ADD CONSTRAINT `trade_ins_ibfk_3` FOREIGN KEY (`aprovado_por_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Update user perfil enum to include financeiro
ALTER TABLE `users` 
MODIFY COLUMN `perfil` ENUM('admin', 'vendedor', 'estoquista', 'financeiro') NOT NULL;