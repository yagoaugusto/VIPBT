USE `viplojabt`;

-- Tabela de Empréstimos de Teste
CREATE TABLE `loans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `vendedor_user_id` INT(11) NOT NULL,
  `status` ENUM('aberto','devolvido','em_atraso','convertido_em_venda') NOT NULL DEFAULT 'aberto',
  `data_saida` DATE NOT NULL,
  `data_prevista_retorno` DATE NULL,
  `data_retorno` DATE NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `vendedor_user_id` (`vendedor_user_id`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`vendedor_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens de Empréstimo
CREATE TABLE `loan_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `loan_id` INT(11) NOT NULL,
  `stock_item_id` INT(11) NOT NULL,
  `estado_saida` VARCHAR(255) NULL,
  `estado_retorno` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`),
  KEY `stock_item_id` (`stock_item_id`),
  CONSTRAINT `loan_items_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_items_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
