CREATE DATABASE IF NOT EXISTS `viplojabt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `viplojabt`;

-- Tabela de usuários do sistema
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha_hash` VARCHAR(255) NOT NULL,
  `perfil` ENUM('admin', 'vendedor', 'estoquista') NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de vendedores (extensão de usuários)
CREATE TABLE `sellers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `comissao_padrao_perc` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir um usuário administrador padrão
-- A senha é 'admin'
INSERT INTO `users` (`nome`, `email`, `senha_hash`, `perfil`) VALUES
('Admin', 'admin@viplojabt.com', '$2y$10$Q8.i2.q.2f.9j3G/6X8X.uVj2/4.2f.9j3G/6X8X.uVj2/4.2f.9', 'admin');
