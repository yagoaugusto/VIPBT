USE `viplojabt`;

-- Tabela de Clientes
CREATE TABLE `customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(20) NULL,
  `cidade` VARCHAR(255) NULL,
  `raquete_entrada_bool` TINYINT(1) NOT NULL DEFAULT 0,
  `raquete_entrada_produto_id` INT(11) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `raquete_entrada_produto_id` (`raquete_entrada_produto_id`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`raquete_entrada_produto_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens em Estoque (unitários ou não)
CREATE TABLE `stock_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `condicao` ENUM('novo','seminovo') NOT NULL,
  `grade` ENUM('A','B','C') NULL,
  `serie` VARCHAR(255) NULL,
  `aquisicao_tipo` ENUM('compra','trade_in','ajuste','retorno_emprestimo') NOT NULL,
  `aquisicao_custo` DECIMAL(10,2) NULL,
  `status` ENUM('em_estoque','reservado','emprestado','vendido','descartado') NOT NULL DEFAULT 'em_estoque',
  `local` VARCHAR(255) NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `stock_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Movimentações de Estoque
CREATE TABLE `inventory_moves` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `stock_item_id` INT(11) NULL,
  `tipo` ENUM('entrada','saida','ajuste','reserva','baixa_reserva','emprestimo_saida','emprestimo_retorno') NOT NULL,
  `qtd` INT(11) NOT NULL,
  `ref_origem` VARCHAR(255) NULL,
  `id_origem` INT(11) NULL,
  `observacao` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `stock_item_id` (`stock_item_id`),
  CONSTRAINT `inventory_moves_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `inventory_moves_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;