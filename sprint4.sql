USE `viplojabt`;

-- Tabela de Pagamentos
CREATE TABLE `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `forma` ENUM('pix','cartao','dinheiro','boleto','transferencia','outros') NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data` DATE NOT NULL,
  `status_pagamento` ENUM('pendente','parcial','pago') NOT NULL DEFAULT 'pendente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Contas a Receber
CREATE TABLE `receivables` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `valor_total` DECIMAL(10,2) NOT NULL,
  `valor_recebido` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `valor_a_receber` DECIMAL(10,2) NOT NULL,
  `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  CONSTRAINT `receivables_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Entradas de Caixa
CREATE TABLE `cash_entries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `origem` VARCHAR(255) NOT NULL,
  `id_origem` INT(11) NULL,
  `forma` ENUM('pix','cartao','dinheiro','boleto','transferencia','outros') NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data` DATE NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Comissões
CREATE TABLE `commissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `seller_id` INT(11) NOT NULL,
  `base_calculo` DECIMAL(10,2) NOT NULL,
  `perc` DECIMAL(5,2) NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `status` ENUM('a_apurar','liberada','paga') NOT NULL DEFAULT 'a_apurar',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `commissions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commissions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
