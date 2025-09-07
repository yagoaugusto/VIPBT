-- Melhorias no Sistema de Empréstimos de Teste
-- Este script atualiza o schema do banco de dados para suportar as melhorias implementadas

USE `viplojabt`;

-- Atualiza o enum de status para incluir 'ativo' como valor padrão
-- e garante que todos os status estão corretos
ALTER TABLE `loans` 
MODIFY COLUMN `status` ENUM('ativo','devolvido','em_atraso','convertido_em_venda') NOT NULL DEFAULT 'ativo';

-- Atualiza empréstimos existentes com status 'aberto' para 'ativo' (se existirem)
UPDATE `loans` SET `status` = 'ativo' WHERE `status` = 'aberto';

-- Adiciona índices para melhorar performance nas consultas
ALTER TABLE `loans` 
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_data_saida` (`data_saida`),
ADD INDEX `idx_data_prevista_retorno` (`data_prevista_retorno`);

-- Adiciona índices na tabela loan_items para melhorar performance
ALTER TABLE `loan_items`
ADD INDEX `idx_estado_retorno` (`estado_retorno`);

-- Verifica se todas as constraints estão em ordem
-- (Este comando apenas informa, não modifica nada)
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE 
    TABLE_SCHEMA = 'viplojabt' 
    AND TABLE_NAME IN ('loans', 'loan_items')
    AND REFERENCED_TABLE_NAME IS NOT NULL;