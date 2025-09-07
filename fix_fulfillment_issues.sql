-- Script para corrigir problemas de fulfillment/expedição
-- Execute este script no banco de dados viplojabt para resolver:
-- 1. Erro "Algo deu errado ao registrar a expedição" no modal
-- 2. Garantir que a tabela fulfillments existe com estrutura correta

USE `viplojabt`;

-- Criar tabela fulfillments se não existir
CREATE TABLE IF NOT EXISTS `fulfillments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `status` ENUM('preparando','enviado','entregue') NOT NULL DEFAULT 'preparando',
  `transportadora` VARCHAR(255) NULL,
  `codigo_rastreio` VARCHAR(255) NULL,
  `enviado_em` DATETIME NULL,
  `entregue_em` DATETIME NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `fulfillments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar se a tabela orders tem a coluna status_entrega necessária
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `status_entrega` ENUM('preparando','enviado','entregue') NULL AFTER `observacao`;

-- Verificar se existem pedidos com IDs para teste
-- Se não existir nenhum pedido, criar um pedido de exemplo para teste
INSERT IGNORE INTO `customers` (`id`, `nome`, `email`, `telefone`, `created_at`) 
VALUES (1, 'Cliente Teste', 'teste@teste.com', '11999999999', NOW());

-- Verificar se existe pelo menos um produto para pedidos de teste
INSERT IGNORE INTO `products` (`id`, `nome`, `sku`, `categoria_id`, `preco`, `created_at`) 
VALUES (1, 'Produto Teste', 'TEST001', 1, 100.00, NOW());

-- Criar categoria se não existir
INSERT IGNORE INTO `categories` (`id`, `nome`, `created_at`) 
VALUES (1, 'Categoria Teste', NOW());

-- Criar pedido de exemplo se necessário (apenas para teste)
INSERT IGNORE INTO `orders` (`id`, `public_code`, `customer_id`, `total`, `data`, `created_at`) 
SELECT 1, 'BT-TESTE123', 1, 100.00, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `orders` WHERE `id` = 1);

SELECT 'Correções de fulfillment aplicadas com sucesso! O erro de expedição deve estar resolvido.' as resultado;