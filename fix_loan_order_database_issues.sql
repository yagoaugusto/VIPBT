-- Script para corrigir os erros nos módulos de empréstimo e pedidos
-- Execute este script no banco de dados viplojabt para resolver:
-- 1. Erro "Column not found: 1054 Unknown column 'p.preco'" em loans/show/1
-- 2. Erro "Table 'viplojabt.fulfillments' doesn't exist" em orders/show/5

USE `viplojabt`;

-- Verificar se a tabela product_prices existe (necessária para preços)
CREATE TABLE IF NOT EXISTS `product_prices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `custo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `preco` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `vigente_desde` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `vigente_desde` (`vigente_desde`),
  CONSTRAINT `product_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela fulfillments se não existir (necessária para gestão de pedidos)
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

-- Verificar se existe pelo menos um preço para cada produto
-- Se um produto não tem preço, criar um registro com preço 0.00
INSERT IGNORE INTO `product_prices` (`product_id`, `custo`, `preco`, `vigente_desde`)
SELECT 
    p.id,
    0.00 as custo,
    0.00 as preco,
    CURDATE() as vigente_desde
FROM `products` p
LEFT JOIN `product_prices` pp ON p.id = pp.product_id
WHERE pp.product_id IS NULL;

-- Verificar se a tabela orders tem a coluna status_entrega necessária
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `status_entrega` ENUM('preparando','enviado','entregue') NULL AFTER `observacao`;

SELECT 'Correções aplicadas com sucesso! Os erros de empréstimo e pedidos devem estar resolvidos.' as resultado;