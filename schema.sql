-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16/09/2025 às 22:54
-- Versão do servidor: 10.4.27-MariaDB
-- Versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `viplojabt`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `brands`
--

INSERT INTO `brands` (`id`, `nome`, `ativo`) VALUES
(1, 'KONNA', 1),
(2, 'HEROES', 1),
(3, 'ZEIQ', 1),
(4, 'QUIQSAND', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cash_entries`
--

CREATE TABLE `cash_entries` (
  `id` int(11) NOT NULL,
  `origem` varchar(255) NOT NULL,
  `id_origem` int(11) DEFAULT NULL,
  `forma` enum('pix','cartao','dinheiro','boleto','transferencia','outros') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cash_entries`
--

INSERT INTO `cash_entries` (`id`, `origem`, `id_origem`, `forma`, `valor`, `data`, `created_at`) VALUES
(1, 'pagamento', 1, 'transferencia', '1000.00', '2025-09-06', '2025-09-06 19:28:35'),
(2, 'pagamento', 1, 'dinheiro', '1000.00', '2025-09-06', '2025-09-06 19:29:05'),
(3, 'pagamento', 5, 'dinheiro', '2000.00', '2025-09-07', '2025-09-07 15:43:52'),
(4, 'pagamento', 40, 'dinheiro', '1799.00', '2025-09-08', '2025-09-08 04:36:08'),
(5, 'pagamento', 2, 'dinheiro', '2000.00', '2025-09-08', '2025-09-08 04:36:31'),
(6, 'pagamento', 39, 'dinheiro', '1500.00', '2025-09-08', '2025-09-08 04:37:40'),
(7, 'pagamento', 36, 'dinheiro', '1500.00', '2025-09-08', '2025-09-08 04:37:58'),
(8, 'pagamento', 42, 'dinheiro', '214.00', '2025-09-08', '2025-09-08 04:52:45'),
(9, 'pagamento', 42, 'dinheiro', '2998.00', '2025-09-08', '2025-09-08 04:53:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `nome`, `ativo`) VALUES
(1, 'RAQUETE', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `channels`
--

CREATE TABLE `channels` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `channels`
--

INSERT INTO `channels` (`id`, `nome`, `ativo`) VALUES
(1, 'Tráfego Pago', 1),
(2, 'Instagram', 1),
(3, 'WhatsApp', 1),
(4, 'Indicação', 1),
(5, 'Loja Física', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `commissions`
--

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `base_calculo` decimal(10,2) NOT NULL,
  `perc` decimal(5,2) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('a_apurar','liberada','paga') NOT NULL DEFAULT 'a_apurar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `raquete_entrada_bool` tinyint(1) NOT NULL DEFAULT 0,
  `raquete_entrada_produto_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `customers`
--

INSERT INTO `customers` (`id`, `nome`, `telefone`, `cidade`, `raquete_entrada_bool`, `raquete_entrada_produto_id`, `created_at`) VALUES
(1, 'YAGO AUGUSTO', '98991668283', 'CANINDE', 0, NULL, '2025-09-06 17:02:34'),
(2, 'Jose', '98991668283', 'Fortaleza', 0, NULL, '2025-09-06 18:20:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fulfillments`
--

CREATE TABLE `fulfillments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('preparando','enviado','entregue') NOT NULL DEFAULT 'preparando',
  `transportadora` varchar(255) DEFAULT NULL,
  `codigo_rastreio` varchar(255) DEFAULT NULL,
  `enviado_em` datetime DEFAULT NULL,
  `entregue_em` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `fulfillments`
--

INSERT INTO `fulfillments` (`id`, `order_id`, `status`, `transportadora`, `codigo_rastreio`, `enviado_em`, `entregue_em`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 1, 'preparando', 'asdda', '23123123', NULL, NULL, 'ok', '2025-09-07 16:34:29', '2025-09-07 16:34:29'),
(2, 1, 'enviado', 'adas', 'asdasd', '2025-09-08 13:35:00', NULL, 'ok', '2025-09-07 16:35:35', '2025-09-07 16:35:35'),
(3, 37, 'enviado', '12323', '1232323', '2025-09-08 00:24:00', NULL, 'foi', '2025-09-08 03:24:30', '2025-09-08 03:24:30'),
(4, 44, 'preparando', '1233', '', NULL, NULL, '12312323', '2025-09-08 05:58:28', '2025-09-08 05:58:28'),
(5, 44, 'enviado', '123123', '12323123', '2025-09-08 02:58:00', NULL, 'ok', '2025-09-08 05:58:40', '2025-09-08 05:58:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `inventory_moves`
--

CREATE TABLE `inventory_moves` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_item_id` int(11) DEFAULT NULL,
  `tipo` enum('entrada','saida','ajuste','reserva','baixa_reserva','emprestimo_saida','emprestimo_retorno') NOT NULL,
  `qtd` int(11) NOT NULL,
  `ref_origem` varchar(255) DEFAULT NULL,
  `id_origem` int(11) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `inventory_moves`
--

INSERT INTO `inventory_moves` (`id`, `product_id`, `stock_item_id`, `tipo`, `qtd`, `ref_origem`, `id_origem`, `observacao`, `created_at`) VALUES
(1, 1, NULL, 'entrada', 5, 'Entrada Manual', NULL, '', '2025-09-06 19:23:16'),
(2, 1, 3, 'saida', 1, 'Venda', 44, 'Conversão de empréstimo em venda', '2025-09-07 03:40:49'),
(3, 1, 1, 'emprestimo_saida', 1, 'Empréstimo', 2, 'Saída para empréstimo de teste', '2025-09-07 03:41:30'),
(4, 2, NULL, 'entrada', 3, 'Entrada Manual', NULL, '', '2025-09-07 04:31:39'),
(5, 2, NULL, 'entrada', 2, 'Entrada Manual', NULL, 'ok', '2025-09-07 04:33:53'),
(6, 2, NULL, 'saida', 1, 'Venda', 2, 'Venda sem item físico específico', '2025-09-07 04:34:16'),
(7, 2, NULL, 'saida', 1, 'Venda', 5, 'Venda sem item físico específico', '2025-09-07 04:34:47'),
(9, 3, NULL, 'entrada', 4, 'Entrada Manual', NULL, 'OK', '2025-09-07 04:53:49'),
(10, 1, 1, 'emprestimo_retorno', 1, 'Devolução de Empréstimo', 2, 'Devolução de empréstimo de teste', '2025-09-07 06:29:39'),
(11, 1, NULL, 'entrada', 1, 'Entrada Manual', NULL, 'ok', '2025-09-07 07:43:15'),
(12, 1, 1, 'saida', 1, 'Venda', 36, 'Item vendido', '2025-09-08 03:20:25'),
(13, 3, 6, 'saida', 1, 'Venda', 37, 'Item vendido', '2025-09-08 03:21:33'),
(14, 4, NULL, 'entrada', 1, 'Entrada Manual', NULL, 'ok', '2025-09-08 03:54:11'),
(15, 4, NULL, 'entrada', 2, 'Entrada Manual', NULL, 'ok', '2025-09-08 03:54:33'),
(16, 1, 11, 'entrada', 1, 'Trade-in', 8, 'Entrada de seminovo via trade-in', '2025-09-08 03:56:05'),
(17, 3, 7, 'saida', 1, 'Venda', 39, 'Item vendido', '2025-09-08 04:01:16'),
(18, 4, NULL, 'entrada', 1, 'Entrada Manual', NULL, 'ok', '2025-09-08 04:10:58'),
(19, 1, NULL, 'entrada', 2, 'Entrada Manual', NULL, 'ok', '2025-09-08 04:11:21'),
(20, 4, NULL, 'entrada', 1, 'Entrada Manual', NULL, 'ok', '2025-09-08 04:11:42'),
(21, 2, NULL, 'entrada', 1, 'Entrada Manual', NULL, 'a mais top', '2025-09-08 04:16:06'),
(22, 5, 15, 'entrada', 1, 'Trade-in', 9, 'Entrada de seminovo via trade-in', '2025-09-08 04:19:44'),
(23, 5, 15, 'saida', 1, 'Venda', 40, 'Item vendido', '2025-09-08 04:20:29'),
(24, 1, 12, 'saida', 1, 'Venda', 41, 'Item vendido', '2025-09-08 04:39:22'),
(25, 2, 14, 'saida', 1, 'Venda', 42, 'Reserva convertida em venda', '2025-09-08 04:51:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vendedor_user_id` int(11) NOT NULL,
  `status` enum('ativo','devolvido','em_atraso','convertido_em_venda') NOT NULL DEFAULT 'ativo',
  `data_saida` date NOT NULL,
  `data_prevista_retorno` date DEFAULT NULL,
  `data_retorno` date DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `loans`
--

INSERT INTO `loans` (`id`, `customer_id`, `vendedor_user_id`, `status`, `data_saida`, `data_prevista_retorno`, `data_retorno`, `order_id`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'convertido_em_venda', '2025-09-07', '2025-09-11', NULL, 44, '', '2025-09-07 03:40:49', '2025-09-08 05:01:48'),
(2, 1, 4, 'devolvido', '2025-09-07', '2025-09-25', '2025-09-07', NULL, 'epa', '2025-09-07 03:41:30', '2025-09-07 06:29:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `loan_items`
--

CREATE TABLE `loan_items` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `stock_item_id` int(11) NOT NULL,
  `estado_saida` varchar(255) DEFAULT NULL,
  `estado_retorno` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `loan_items`
--

INSERT INTO `loan_items` (`id`, `loan_id`, `stock_item_id`, `estado_saida`, `estado_retorno`) VALUES
(1, 1, 3, 'ok', NULL),
(2, 2, 1, 'beleza', 'ok');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `public_code` varchar(15) NOT NULL,
  `status_pedido` enum('novo','confirmado','vendido','cancelado') NOT NULL DEFAULT 'novo',
  `status_fiscal` enum('nao_faturado','faturado') NOT NULL DEFAULT 'nao_faturado',
  `status_entrega` enum('nao_entregue','preparando','enviado','entregue','entrega_parcial') NOT NULL DEFAULT 'nao_entregue',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_confirmacao_venda` timestamp NULL DEFAULT NULL COMMENT 'Data quando o pedido foi confirmado como venda',
  `confirmado_por` int(11) DEFAULT NULL COMMENT 'ID do usuário que confirmou a venda'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `seller_id`, `channel_id`, `data`, `public_code`, `status_pedido`, `status_fiscal`, `status_entrega`, `total`, `observacao`, `created_at`, `updated_at`, `data_confirmacao_venda`, `confirmado_por`) VALUES
(1, 2, 1, 4, '2025-09-06', 'BT-OYTETJOC', 'vendido', 'faturado', 'entregue', '0.00', 'ok', '2025-09-06 19:27:45', '2025-09-07 16:35:43', '2025-09-07 15:42:59', 4),
(2, 2, 1, 5, '2025-09-07', 'BT-3704BOC6', 'cancelado', 'faturado', 'entregue', '0.00', '', '2025-09-07 04:34:16', '2025-09-07 15:14:27', NULL, NULL),
(5, 2, 1, 5, '2025-09-07', 'BT-A70GIE3S', 'confirmado', 'faturado', 'entregue', '0.00', '', '2025-09-07 04:34:47', '2025-09-07 15:12:05', NULL, NULL),
(36, 2, 1, 4, '2025-09-08', 'BT-RYQZTE29', 'novo', 'nao_faturado', 'nao_entregue', '2000.00', 'ok', '2025-09-08 03:20:25', '2025-09-08 03:20:25', NULL, NULL),
(37, 1, 1, 4, '2025-09-08', 'BT-EMV8W6VU', 'vendido', 'nao_faturado', 'enviado', '2000.00', 'ok', '2025-09-08 03:21:33', '2025-09-08 03:24:30', '2025-09-08 03:24:16', 4),
(39, 1, 1, 4, '2025-09-08', 'BT-D6ZRFF0X', 'confirmado', 'nao_faturado', 'nao_entregue', '2000.00', '', '2025-09-08 04:01:16', '2025-09-08 04:02:21', NULL, NULL),
(40, 2, 1, 5, '2025-09-08', 'BT-6WI0MRD5', 'novo', 'nao_faturado', 'nao_entregue', '1899.00', 'FOI', '2025-09-08 04:20:29', '2025-09-08 04:20:29', NULL, NULL),
(41, 2, 1, 4, '2025-09-08', 'BT-7RBPNCX7', 'confirmado', 'nao_faturado', 'nao_entregue', '3333.00', '', '2025-09-08 04:39:22', '2025-09-08 04:40:06', NULL, NULL),
(42, 1, 1, 4, '2025-09-08', 'BT-6SK3ZWTD', 'vendido', 'nao_faturado', 'nao_entregue', '3212.00', '', '2025-09-08 04:51:15', '2025-09-08 04:52:26', '2025-09-08 04:52:26', 4),
(44, 2, 1, 2, '2025-09-08', 'BT-YN205LE8', 'vendido', 'faturado', 'entregue', '0.00', 'Conversão de empréstimo #1', '2025-09-08 05:01:48', '2025-09-08 05:59:39', '2025-09-08 05:02:58', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_credits`
--

CREATE TABLE `order_credits` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `origem` enum('trade_in','ajuste') NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `trade_in_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `order_credits`
--

INSERT INTO `order_credits` (`id`, `order_id`, `origem`, `descricao`, `valor`, `trade_in_id`, `created_at`) VALUES
(1, 37, 'trade_in', 'Crédito de Trade-in #6', '2000.00', 6, '2025-09-08 03:21:33'),
(2, 40, 'trade_in', 'Crédito de Trade-in #4', '100.00', 4, '2025-09-08 04:20:29'),
(3, 42, 'trade_in', 'Crédito de Trade-in #8', '800.00', 8, '2025-09-08 04:51:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_item_id` int(11) DEFAULT NULL,
  `qtd` int(11) NOT NULL,
  `preco_unit` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `stock_item_id`, `qtd`, `preco_unit`, `desconto`) VALUES
(1, 1, 1, NULL, 1, '2000.00', '0.00'),
(2, 2, 2, NULL, 1, '2000.00', '0.00'),
(5, 5, 2, NULL, 1, '2000.00', '0.00'),
(12, 36, 1, NULL, 1, '2000.00', '0.00'),
(13, 37, 3, NULL, 1, '2000.00', '0.00'),
(14, 39, 3, NULL, 1, '2000.00', '0.00'),
(15, 40, 5, NULL, 1, '1899.00', '0.00'),
(16, 41, 1, 12, 1, '3333.00', '0.00'),
(17, 42, 2, 14, 1, '3212.00', '0.00'),
(18, 44, 1, 3, 1, '0.00', '0.00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `forma` enum('pix','cartao','dinheiro','boleto','transferencia','outros') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `status_pagamento` enum('pendente','parcial','pago') NOT NULL DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `forma`, `valor`, `data`, `status_pagamento`, `created_at`) VALUES
(1, 1, 'transferencia', '1000.00', '2025-09-06', 'parcial', '2025-09-06 19:28:35'),
(2, 1, 'dinheiro', '1000.00', '2025-09-06', 'pago', '2025-09-06 19:29:05'),
(3, 5, 'dinheiro', '2000.00', '2025-09-07', 'pago', '2025-09-07 15:43:52'),
(4, 40, 'dinheiro', '1799.00', '2025-09-08', 'pago', '2025-09-08 04:36:08'),
(5, 2, 'dinheiro', '2000.00', '2025-09-08', 'pago', '2025-09-08 04:36:31'),
(6, 39, 'dinheiro', '1500.00', '2025-09-08', 'parcial', '2025-09-08 04:37:40'),
(7, 36, 'dinheiro', '1500.00', '2025-09-08', 'pago', '2025-09-08 04:37:58'),
(8, 42, 'dinheiro', '214.00', '2025-09-08', 'parcial', '2025-09-08 04:52:45'),
(9, 42, 'dinheiro', '2998.00', '2025-09-08', 'pago', '2025-09-08 04:53:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `tipo_condicao` enum('novo','seminovo') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `sku`, `nome`, `brand_id`, `category_id`, `tipo_condicao`, `ativo`) VALUES
(1, 'RK-KONNA-MAV-VERDE', 'Maverick Semi Branca', 1, 1, 'novo', 1),
(2, 'RQT-STARLIGHT-SEMINOVA', 'STARLIGHT', 2, 1, 'seminovo', 1),
(3, 'RQT ZEIQ', 'ZEIQ RAGNAROK', 3, 1, 'novo', 1),
(4, 'RQT SEMI MAV', 'Maverick', 1, 1, 'seminovo', 1),
(5, 'RAT QUIQ SEMI', 'QUIQSAND SUPER SEMI', 4, 1, 'seminovo', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_prices`
--

CREATE TABLE `product_prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `custo` decimal(10,2) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `vigente_desde` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `product_prices`
--

INSERT INTO `product_prices` (`id`, `product_id`, `custo`, `preco`, `vigente_desde`) VALUES
(1, 1, '800.00', '2000.00', '2025-09-06'),
(2, 2, '800.00', '2000.00', '2025-09-06'),
(3, 3, '1000.00', '2000.00', '2025-09-07'),
(4, 4, '800.00', '1200.00', '2025-09-08'),
(5, 1, '800.00', '800.00', '2025-09-08'),
(6, 5, '899.00', '1899.00', '2025-09-08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `receivables`
--

CREATE TABLE `receivables` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `valor_recebido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_a_receber` decimal(10,2) NOT NULL,
  `data_cobranca` date DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `receivables`
--

INSERT INTO `receivables` (`id`, `order_id`, `valor_total`, `valor_recebido`, `valor_a_receber`, `data_cobranca`, `atualizado_em`) VALUES
(1, 1, '2000.00', '2000.00', '0.00', NULL, '2025-09-06 19:29:05'),
(2, 2, '2000.00', '2000.00', '0.00', NULL, '2025-09-08 04:36:31'),
(3, 5, '2000.00', '2000.00', '0.00', NULL, '2025-09-07 15:43:52'),
(4, 36, '2000.00', '1500.00', '500.00', NULL, '2025-09-08 04:37:58'),
(5, 37, '2000.00', '0.00', '0.00', NULL, '2025-09-08 03:21:33'),
(6, 39, '2000.00', '1500.00', '500.00', NULL, '2025-09-08 04:37:40'),
(7, 40, '1899.00', '1799.00', '100.00', NULL, '2025-09-08 04:36:08'),
(8, 41, '3333.00', '0.00', '3333.00', NULL, '2025-09-08 04:39:22'),
(9, 42, '3212.00', '3212.00', '0.00', '2025-09-24', '2025-09-08 20:19:40'),
(10, 44, '0.00', '0.00', '0.00', '2025-09-08', '2025-09-08 20:19:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comissao_padrao_perc` decimal(5,2) NOT NULL DEFAULT 5.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `comissao_padrao_perc`) VALUES
(1, 5, '25.00'),
(2, 6, '5.00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `stock_items`
--

CREATE TABLE `stock_items` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `condicao` enum('novo','seminovo') NOT NULL,
  `grade` enum('A','B','C') DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `aquisicao_tipo` enum('compra','trade_in','ajuste','retorno_emprestimo') NOT NULL,
  `aquisicao_custo` decimal(10,2) DEFAULT NULL,
  `preco_venda` decimal(10,2) DEFAULT NULL,
  `status` enum('em_estoque','reservado','emprestado','vendido','descartado') NOT NULL DEFAULT 'em_estoque',
  `local` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `stock_items`
--

INSERT INTO `stock_items` (`id`, `product_id`, `condicao`, `grade`, `serie`, `aquisicao_tipo`, `aquisicao_custo`, `preco_venda`, `status`, `local`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 1, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'vendido', NULL, NULL, '2025-09-06 19:23:16', '2025-09-08 03:20:25'),
(2, 1, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'em_estoque', NULL, NULL, '2025-09-06 19:23:16', '2025-09-06 19:23:16'),
(3, 1, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'vendido', NULL, NULL, '2025-09-06 19:23:16', '2025-09-08 05:02:58'),
(4, 1, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'em_estoque', NULL, NULL, '2025-09-06 19:23:16', '2025-09-06 19:23:16'),
(5, 1, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'em_estoque', NULL, NULL, '2025-09-06 19:23:16', '2025-09-06 19:23:16'),
(6, 3, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'vendido', NULL, NULL, '2025-09-07 04:53:49', '2025-09-08 03:21:33'),
(7, 3, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'vendido', NULL, NULL, '2025-09-07 04:53:49', '2025-09-08 04:01:16'),
(8, 3, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'em_estoque', NULL, NULL, '2025-09-07 04:53:49', '2025-09-07 04:53:49'),
(9, 3, 'novo', NULL, NULL, 'compra', '1000.00', NULL, 'em_estoque', NULL, NULL, '2025-09-07 04:53:49', '2025-09-07 04:53:49'),
(10, 1, 'novo', NULL, NULL, 'compra', '900.00', NULL, 'em_estoque', NULL, NULL, '2025-09-07 07:43:15', '2025-09-07 07:43:15'),
(11, 1, 'seminovo', 'A', '', 'trade_in', '800.00', NULL, 'em_estoque', NULL, NULL, '2025-09-08 03:56:05', '2025-09-08 03:56:05'),
(12, 1, 'novo', NULL, NULL, 'compra', '999.00', '3333.00', 'vendido', NULL, NULL, '2025-09-08 04:11:21', '2025-09-08 04:39:22'),
(13, 1, 'novo', NULL, NULL, 'compra', '999.00', '3333.00', 'em_estoque', NULL, NULL, '2025-09-08 04:11:21', '2025-09-08 04:11:21'),
(14, 2, 'seminovo', NULL, NULL, 'compra', '876.00', '3212.00', 'vendido', NULL, NULL, '2025-09-08 04:16:06', '2025-09-08 04:52:26'),
(15, 5, 'seminovo', 'B', '1232332', 'trade_in', '899.00', '1899.00', 'vendido', NULL, NULL, '2025-09-08 04:19:44', '2025-09-08 04:20:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `trade_ins`
--

CREATE TABLE `trade_ins` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` enum('pendente','aprovado','reprovado','creditado') NOT NULL DEFAULT 'pendente',
  `avaliador_user_id` int(11) DEFAULT NULL,
  `observacoes_aprovacao` text DEFAULT NULL,
  `aprovado_por_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `trade_ins`
--

INSERT INTO `trade_ins` (`id`, `customer_id`, `status`, `avaliador_user_id`, `observacoes_aprovacao`, `aprovado_por_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'reprovado', 4, NULL, 4, '2025-09-06 20:56:33', '2025-09-07 07:40:31'),
(2, 2, 'aprovado', 4, NULL, 4, '2025-09-06 20:58:16', '2025-09-08 03:22:36'),
(3, 2, 'reprovado', 4, NULL, 4, '2025-09-06 20:58:22', '2025-09-08 03:22:46'),
(4, 2, 'creditado', 4, NULL, 4, '2025-09-06 21:06:42', '2025-09-08 04:20:29'),
(5, 1, 'aprovado', 4, 'ok', 4, '2025-09-07 04:54:39', '2025-09-07 05:48:29'),
(6, 1, 'creditado', 4, NULL, 4, '2025-09-07 07:41:38', '2025-09-08 03:21:33'),
(7, 1, 'aprovado', 4, 'ok', 4, '2025-09-08 03:26:09', '2025-09-08 03:26:24'),
(8, 1, 'creditado', 4, NULL, 4, '2025-09-08 03:46:14', '2025-09-08 04:51:15'),
(9, 1, 'aprovado', 4, NULL, 4, '2025-09-08 04:19:31', '2025-09-08 04:19:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `trade_in_items`
--

CREATE TABLE `trade_in_items` (
  `id` int(11) NOT NULL,
  `trade_in_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `product_model_id` int(11) DEFAULT NULL,
  `modelo_texto` varchar(255) DEFAULT NULL,
  `grade` enum('A','B','C') DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `avaliacao_valor` decimal(10,2) NOT NULL,
  `valor_creditado` decimal(10,2) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `stock_item_resultante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `trade_in_items`
--

INSERT INTO `trade_in_items` (`id`, `trade_in_id`, `brand_id`, `product_model_id`, `modelo_texto`, `grade`, `serie`, `avaliacao_valor`, `valor_creditado`, `observacoes`, `stock_item_resultante_id`) VALUES
(1, 4, 2, 2, '', 'A', '213', '200.00', '100.00', '', NULL),
(2, 5, 3, 3, '', 'A', '12323', '900.00', '1200.00', 'OK', NULL),
(3, 6, 1, 1, 'Mav ok', 'A', 'xxxx', '3000.00', '2000.00', 'ok', NULL),
(4, 7, 2, 2, 'aoskdoapskd ', 'A', '1323123', '900.00', '800.00', '', NULL),
(5, 8, 1, 1, '123123', 'A', '', '800.00', '800.00', 'ok', 11),
(6, 9, 4, 5, 'OK', 'B', '1232332', '1899.00', '899.00', 'SHOW', 15);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor','estoquista','financeiro') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `created_at`) VALUES
(4, 'Admin', 'admin@viplojabt.com', '$2y$10$4D20b9m.s0Gh1CbiKXeAGuOCkBrOs1D95Gb1i.iLxLTgg/RqWMUUG', 'admin', 1, '2025-09-06 18:59:43'),
(5, 'yago augusto', 'yagoacp@gmail.com', '$2y$10$4D20b9m.s0Gh1CbiKXeAGuOCkBrOs1D95Gb1i.iLxLTgg/RqWMUUG', 'admin', 1, '2025-09-06 19:22:43'),
(6, 'Pedro', 'pedro@a.com', '$2y$10$95G4UMOPV6EZ0PGFEfPbLOwQe23lOmxROTbO1VAu3AiuBm1WxiT5u', 'vendedor', 1, '2025-09-08 05:56:30');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cash_entries`
--
ALTER TABLE `cash_entries`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Índices de tabela `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `raquete_entrada_produto_id` (`raquete_entrada_produto_id`);

--
-- Índices de tabela `fulfillments`
--
ALTER TABLE `fulfillments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `inventory_moves`
--
ALTER TABLE `inventory_moves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `stock_item_id` (`stock_item_id`),
  ADD KEY `idx_inventory_moves_product_tipo` (`product_id`,`tipo`);

--
-- Índices de tabela `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vendedor_user_id` (`vendedor_user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data_saida` (`data_saida`),
  ADD KEY `idx_data_prevista_retorno` (`data_prevista_retorno`);

--
-- Índices de tabela `loan_items`
--
ALTER TABLE `loan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `stock_item_id` (`stock_item_id`),
  ADD KEY `idx_estado_retorno` (`estado_retorno`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_code` (`public_code`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `idx_status_pedido` (`status_pedido`),
  ADD KEY `idx_data_confirmacao` (`data_confirmacao_venda`),
  ADD KEY `orders_confirmado_por_fk` (`confirmado_por`);

--
-- Índices de tabela `order_credits`
--
ALTER TABLE `order_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `trade_in_id` (`trade_in_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `stock_item_id` (`stock_item_id`);

--
-- Índices de tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices de tabela `product_prices`
--
ALTER TABLE `product_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `receivables`
--
ALTER TABLE `receivables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_receivables_data_cobranca` (`data_cobranca`);

--
-- Índices de tabela `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_stock_items_product_status` (`product_id`,`status`);

--
-- Índices de tabela `trade_ins`
--
ALTER TABLE `trade_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `avaliador_user_id` (`avaliador_user_id`),
  ADD KEY `aprovado_por_user_id` (`aprovado_por_user_id`);

--
-- Índices de tabela `trade_in_items`
--
ALTER TABLE `trade_in_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trade_in_id` (`trade_in_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `product_model_id` (`product_model_id`),
  ADD KEY `stock_item_resultante_id` (`stock_item_resultante_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cash_entries`
--
ALTER TABLE `cash_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `channels`
--
ALTER TABLE `channels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `fulfillments`
--
ALTER TABLE `fulfillments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `inventory_moves`
--
ALTER TABLE `inventory_moves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `loan_items`
--
ALTER TABLE `loan_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `order_credits`
--
ALTER TABLE `order_credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `product_prices`
--
ALTER TABLE `product_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `receivables`
--
ALTER TABLE `receivables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `stock_items`
--
ALTER TABLE `stock_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `trade_ins`
--
ALTER TABLE `trade_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `trade_in_items`
--
ALTER TABLE `trade_in_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `commissions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commissions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`);

--
-- Restrições para tabelas `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`raquete_entrada_produto_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `fulfillments`
--
ALTER TABLE `fulfillments`
  ADD CONSTRAINT `fulfillments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `inventory_moves`
--
ALTER TABLE `inventory_moves`
  ADD CONSTRAINT `inventory_moves_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_moves_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`vendedor_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `loan_items`
--
ALTER TABLE `loan_items`
  ADD CONSTRAINT `loan_items_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_items_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`);

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_confirmado_por_fk` FOREIGN KEY (`confirmado_por`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`);

--
-- Restrições para tabelas `order_credits`
--
ALTER TABLE `order_credits`
  ADD CONSTRAINT `order_credits_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_credits_ibfk_2` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`);

--
-- Restrições para tabelas `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Restrições para tabelas `product_prices`
--
ALTER TABLE `product_prices`
  ADD CONSTRAINT `product_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `receivables`
--
ALTER TABLE `receivables`
  ADD CONSTRAINT `receivables_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `stock_items`
--
ALTER TABLE `stock_items`
  ADD CONSTRAINT `stock_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `trade_ins`
--
ALTER TABLE `trade_ins`
  ADD CONSTRAINT `trade_ins_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `trade_ins_ibfk_2` FOREIGN KEY (`avaliador_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trade_ins_ibfk_3` FOREIGN KEY (`aprovado_por_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `trade_in_items`
--
ALTER TABLE `trade_in_items`
  ADD CONSTRAINT `trade_in_items_ibfk_1` FOREIGN KEY (`trade_in_id`) REFERENCES `trade_ins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_in_items_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `trade_in_items_ibfk_3` FOREIGN KEY (`product_model_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `trade_in_items_ibfk_4` FOREIGN KEY (`stock_item_resultante_id`) REFERENCES `stock_items` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
