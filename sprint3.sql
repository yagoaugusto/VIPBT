USE `viplojabt`;

-- Tabela de Canais de Venda
CREATE TABLE `channels` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir alguns canais padrão
INSERT INTO `channels` (`nome`, `ativo`) VALUES
('Tráfego Pago', 1),
('Instagram', 1),
('WhatsApp', 1),
('Indicação', 1),
('Loja Física', 1);

-- Tabela de Pedidos
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `seller_id` INT(11) NOT NULL,
  `channel_id` INT(11) NOT NULL,
  `data` DATE NOT NULL,
  `public_code` VARCHAR(10) NOT NULL,
  `status_pedido` ENUM('novo','faturado','cancelado') NOT NULL DEFAULT 'novo',
  `status_fiscal` ENUM('nao_faturado','faturado') NOT NULL DEFAULT 'nao_faturado',
  `status_entrega` ENUM('nao_entregue','preparando','enviado','entregue','entrega_parcial') NOT NULL DEFAULT 'nao_entregue',
  `observacao` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_code` (`public_code`),
  KEY `customer_id` (`customer_id`),
  KEY `seller_id` (`seller_id`),
  KEY `channel_id` (`channel_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens do Pedido
CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `stock_item_id` INT(11) NULL,
  `qtd` INT(11) NOT NULL,
  `preco_unit` DECIMAL(10,2) NOT NULL,
  `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `stock_item_id` (`stock_item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;