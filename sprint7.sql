USE `viplojabt`;

-- Tabela de Fulfillments (Expedição)
CREATE TABLE `fulfillments` (
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
