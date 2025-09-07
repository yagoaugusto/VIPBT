USE `viplojabt`;

-- Tabela de Créditos de Pedido (para Trade-in ou ajustes)
CREATE TABLE `order_credits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `origem` ENUM('trade_in','ajuste') NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_credits_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Avaliações de Trade-in
CREATE TABLE `trade_ins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `status` ENUM('pendente','aprovado','reprovado','creditado') NOT NULL DEFAULT 'pendente',
  `avaliador_user_id` INT(11) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `avaliador_user_id` (`avaliador_user_id`),
  CONSTRAINT `trade_ins_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `trade_ins_ibfk_2` FOREIGN KEY (`avaliador_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens de Trade-in
CREATE TABLE `trade_in_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `trade_in_id` INT(11) NOT NULL,
  `brand_id` INT(11) NULL,
  `product_model_id` INT(11) NULL,
  `modelo_texto` VARCHAR(255) NULL,
  `grade` ENUM('A','B','C') NULL,
  `serie` VARCHAR(255) NULL,
  `avaliacao_valor` DECIMAL(10,2) NOT NULL,
  `valor_creditado` DECIMAL(10,2) NOT NULL,
  `observacoes` TEXT NULL,
  `stock_item_resultante_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  KEY `trade_in_id` (`trade_in_id`),
  KEY `brand_id` (`brand_id`),
  KEY `product_model_id` (`product_model_id`),
  KEY `stock_item_resultante_id` (`stock_item_resultante_id`),
  CONSTRAINT `trade_in_items_ibfk_1` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trade_in_items_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `trade_in_items_ibfk_3` FOREIGN KEY (`product_model_id`) REFERENCES `products` (`id`),
  CONSTRAINT `trade_in_items_ibfk_4` FOREIGN KEY (`stock_item_resultante_id`) REFERENCES `stock_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
