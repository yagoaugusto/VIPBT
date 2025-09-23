# VIPBT - Sistema de Gestão para Loja de Beach Tennis

![VIPBT](https://img.shields.io/badge/VIPBT-v2.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-green.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![Status](https://img.shields.io/badge/Status-Produção-brightgreen.svg)

**VIPBT** é um sistema completo de gestão empresarial desenvolvido especificamente para lojas de Beach Tennis. O sistema oferece controle total sobre vendas, empréstimos de teste, estoque, indicadores financeiros e muito mais.

![Sistema VIPBT](vipbt-overview.png)

## 🚀 Quick Start

```bash
# 1. Clone o repositório
git clone https://github.com/yagoaugusto/VIPBT.git
cd VIPBT

# 2. Configure o banco de dados
mysql -u root -p -e "CREATE DATABASE viplojabt;"
mysql -u root -p viplojabt < viplojabt.sql

# 3. Configure a aplicação
cp config/config.php.example config/config.php
# Edite config/config.php com suas configurações

# 4. Configure o servidor web
# Aponte DocumentRoot para /caminho/para/VIPBT/public/

# 5. Acesse o sistema
# http://localhost/VIPBT
```

## 📋 Índice

- [Funcionalidades](#-funcionalidades)
- [Benefícios do Sistema](#-benefícios-do-sistema)
- [Instalação](#-instalação)
- [Como Usar](#-como-usar)
- [Estrutura do Sistema](#-estrutura-do-sistema)
- [Tecnologias](#-tecnologias)
- [Configuração](#-configuração)
- [Contribuição](#-contribuição)

## 🚀 Funcionalidades

### 1. **Gestão de Vendas Completa**
- ✅ **Criação de Pedidos**: Interface intuitiva para criação de pedidos com validação completa
- ✅ **Controle de Status**: Acompanhamento do ciclo completo (novo → confirmado → vendido/cancelado)
- ✅ **Rastreamento de Conversão**: Análise detalhada de taxa de conversão de pedidos em vendas
- ✅ **Consulta Pública**: Clientes podem consultar status dos pedidos via código público
- ✅ **Múltiplos Canais**: Suporte a diferentes canais de venda (Loja Física, Online, WhatsApp)

### 2. **Sistema de Empréstimos de Teste**
- 🎾 **Empréstimo de Equipamentos**: Controle completo de raquetes emprestadas para teste
- 📅 **Gestão de Prazos**: Controle de datas de saída e previsão de retorno
- 🔄 **Conversão em Vendas**: Funcionalidade para converter empréstimos em vendas diretas
- 📊 **Relatórios de Empréstimos**: Acompanhamento de performance e conversões
- ⚠️ **Alertas de Atraso**: Identificação visual de empréstimos em atraso

### 3. **Controle de Estoque Inteligente**
- 📦 **Entrada de Produtos**: Registro detalhado de entrada de mercadorias
- 💰 **Precificação Dinâmica**: Controle de custo e preço de venda por item
- 📊 **Saldos em Tempo Real**: Visualização de disponibilidade atual
- 🔄 **Integração com Vendas**: Baixa automática no estoque ao confirmar vendas
- ⚡ **Validação de Disponibilidade**: Verificação automática antes de processar vendas

### 4. **Trade-in (Avaliação de Produtos Usados)**
- 🔄 **Avaliação de Produtos**: Sistema para avaliar equipamentos usados
- 💵 **Cálculo de Valor**: Determinação do valor de troca baseado em critérios
- 📝 **Histórico de Avaliações**: Registro completo de todas as avaliações
- 🏷️ **Integração com Vendas**: Uso do valor do trade-in como desconto em novas compras

### 5. **Indicadores Financeiros Avançados**
- 📈 **Dashboard Financeiro**: Visão geral de receitas, lucros e margens
- 👥 **Performance por Vendedor**: Análise detalhada de vendas por colaborador
- 📺 **Análise por Canal**: Comparativo de performance entre canais de venda
- 🎯 **Clientes Top**: Ranking de melhores clientes por compras e empréstimos
- 📊 **Produtos Mais Vendidos**: Relatórios de produtos com maior saída
- 💳 **Métodos de Pagamento**: Análise de preferências de pagamento dos clientes

### 6. **Gestão de Clientes**
- 👤 **Cadastro Completo**: Informações detalhadas de clientes
- 📞 **Histórico de Contatos**: Registro de interações e comunicações
- 🛒 **Histórico de Compras**: Acompanhamento completo de pedidos por cliente
- 🎾 **Histórico de Empréstimos**: Controle de todos os equipamentos emprestados

### 7. **Sistema de Usuários e Permissões**
- 🔐 **Controle de Acesso**: Diferentes níveis de permissão (Admin, Vendedor, Estoquista, Financeiro)
- 👨‍💼 **Gestão de Vendedores**: Cadastro e controle de equipe de vendas
- 📊 **Relatórios por Usuário**: Análise individual de performance

### 8. **Logística e Fulfillment**
- 📦 **Controle de Entregas**: Gestão de status de entrega dos pedidos
- 🚚 **Rastreamento**: Acompanhamento de envios e entregas
- 📍 **Gestão de Endereços**: Controle de endereços de entrega

## 💡 Benefícios do Sistema

### **Para o Negócio:**
- 📊 **Decisões Baseadas em Dados**: Relatórios detalhados para tomada de decisão
- 💰 **Aumento da Conversão**: Controle preciso do funil de vendas
- ⏱️ **Redução de Tempo**: Automação de processos manuais
- 🎯 **Foco no Cliente**: Histórico completo para melhor atendimento
- 📈 **Crescimento Sustentável**: Escalabilidade para crescimento do negócio

### **Para a Operação:**
- ✅ **Redução de Erros**: Validações automáticas em todos os processos
- 🔄 **Integração Total**: Todos os módulos trabalham de forma integrada
- 📱 **Interface Intuitiva**: Fácil de usar, reduz tempo de treinamento
- 🔍 **Rastreabilidade**: Auditoria completa de todas as operações
- ⚡ **Performance**: Sistema otimizado para alta performance

### **Para os Clientes:**
- 🌐 **Consulta Online**: Acompanhamento de pedidos via web
- 🎾 **Teste Sem Compromisso**: Sistema de empréstimo para testar equipamentos
- 💳 **Múltiplas Formas de Pagamento**: Flexibilidade nas formas de pagamento
- 🔄 **Trade-in Facilitado**: Processo simplificado para troca de equipamentos

## 🛠️ Instalação

### Pré-requisitos
- PHP 8.3 ou superior (recomendado)
- MySQL 5.7+ ou MariaDB 10.3+
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL, JSON, mbstring
- Composer (opcional, para dependências futuras)

### Passo a Passo

1. **Clone o repositório:**
```bash
git clone https://github.com/yagoaugusto/VIPBT.git
cd VIPBT
```

2. **Configure o banco de dados:**
```sql
-- Crie o banco de dados
CREATE DATABASE viplojabt;

-- Execute o script de estrutura
mysql -u root -p viplojabt < viplojabt.sql
```

3. **Configure a aplicação:**
```php
// Edite config/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'viplojabt');
define('URL_ROOT', 'http://localhost/VIPBT');
```

4. **Configure o servidor web:**
   - Aponte o DocumentRoot para a pasta `public/`
   - Configure o arquivo `.htaccess` para reescrita de URLs

5. **Acesso inicial:**
   - URL: `http://localhost/VIPBT`
   - Usuário padrão: `admin`
   - Senha: (configurar no primeiro acesso)

## 📖 Como Usar

### **1. Gestão de Vendas**

#### Criar um Novo Pedido:
1. Acesse `Vendas > Novo Pedido`
2. Selecione o cliente (ou cadastre um novo)
3. Escolha o vendedor e canal de venda
4. Adicione produtos ao pedido:
   - Use o botão "Adicionar Item"
   - Busque produtos por nome ou código
   - Defina quantidade e preço
5. Revise o pedido e salve
6. O sistema gera automaticamente um código público para consulta

#### Confirmar Venda:
1. Acesse `Vendas > Listar Pedidos`
2. Encontre o pedido desejado
3. Clique em "Confirmar como Venda"
4. O status mudará para "Vendido" e o estoque será atualizado

#### Consulta Pública:
- Clientes acessam: `http://seusite.com/public/consulta`
- Inserem o código público do pedido
- Visualizam status, itens e timeline de entrega

### **2. Sistema de Empréstimos**

#### Criar Empréstimo:
1. Acesse `Empréstimos > Novo Empréstimo`
2. Selecione o cliente
3. Defina data de saída e previsão de retorno
4. Adicione produtos do estoque disponível
5. Salve o empréstimo

#### Controlar Devolução:
1. Acesse `Empréstimos > Listar`
2. Encontre o empréstimo ativo
3. Registre a devolução com estado do produto
4. Ou converta diretamente em venda

#### Converter em Venda:
1. No empréstimo ativo, clique em "Converter em Venda"
2. Ajuste preços se necessário
3. Confirme a conversão
4. Sistema cria automaticamente o pedido de venda

### **3. Controle de Estoque**

#### Entrada de Produtos:
1. Acesse `Estoque > Nova Entrada`
2. Selecione o produto
3. Informe quantidade, custo e preço de venda
4. Adicione observações se necessário
5. Salve a entrada

#### Consultar Saldos:
1. Acesse `Estoque > Saldos`
2. Visualize disponibilidade de todos os produtos
3. Use filtros para buscar produtos específicos

#### Verificar Movimentação:
1. Acesse `Estoque > Movimentação`
2. Acompanhe entradas, saídas e transferências
3. Analise histórico de movimentações

### **4. Indicadores Financeiros**

#### Dashboard Principal:
1. Acesse `Financeiro > Indicadores`
2. Configure período de análise
3. Visualize métricas principais:
   - Total de vendas
   - Lucratividade
   - Taxa de conversão
   - Produtos mais vendidos

#### Análise por Vendedor:
1. Use filtros para selecionar vendedor específico
2. Compare performance entre períodos
3. Analise produtos vendidos por cada um

#### Relatórios Customizados:
1. Configure filtros de data, canal e vendedor
2. Exporte dados para análise externa
3. Agende relatórios periódicos

### **5. Trade-in**

#### Nova Avaliação:
1. Acesse `Trade-in > Nova Avaliação`
2. Selecione cliente e produto
3. Defina critérios de avaliação
4. Calcule valor de troca
5. Registre a avaliação

#### Usar em Vendas:
1. Durante criação de pedido
2. Aplicar valor de trade-in como desconto
3. Sistema registra a transação

## 📁 Estrutura do Sistema

```
VIPBT/
├── config/              # Configurações do sistema
│   ├── config.php       # Configurações principais
│   └── config_dev.php   # Configurações de desenvolvimento
├── controllers/         # Controladores MVC
│   ├── Orders.php       # Gestão de pedidos/vendas
│   ├── Loans.php        # Sistema de empréstimos
│   ├── Stock.php        # Controle de estoque
│   ├── Financial.php    # Indicadores financeiros
│   ├── TradeIns.php     # Sistema de trade-in
│   └── ...
├── models/              # Modelos de dados
│   ├── OrderModel.php   # Modelo de pedidos
│   ├── LoanModel.php    # Modelo de empréstimos
│   ├── StockModel.php   # Modelo de estoque
│   └── ...
├── views/               # Interface do usuário
│   ├── orders/          # Telas de vendas
│   ├── loans/           # Telas de empréstimos
│   ├── stock/           # Telas de estoque
│   └── ...
├── public/              # Arquivos públicos
│   ├── css/             # Estilos
│   ├── js/              # JavaScript
│   └── images/          # Imagens
├── core/                # Core do framework
└── database/            # Scripts SQL
```

## 🔧 Tecnologias

- **Backend**: PHP 8.3+ com arquitetura MVC customizada
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 5
- **Banco de Dados**: MySQL 5.7+ / MariaDB 10.3+
- **Componentes**: Chart.js para gráficos, Font Awesome para ícones
- **Segurança**: Sistema de sessões, validação de entrada, proteção CSRF
- **APIs**: RESTful endpoints para integração externa

## ⚙️ Configuração

### Configurações Básicas

#### config/config.php:
```php
// Banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'senha');
define('DB_NAME', 'viplojabt');

// URLs
define('URL_ROOT', 'http://localhost/VIPBT');
define('SITE_NAME', 'VIP LOJA BT');
```

### Permissões de Usuário

O sistema possui 4 níveis de acesso:

1. **Admin**: Acesso total ao sistema
2. **Vendedor**: Vendas, empréstimos, consulta de estoque
3. **Estoquista**: Estoque, empréstimos, trade-in
4. **Financeiro**: Relatórios financeiros, indicadores

### Personalização

#### Canais de Venda:
Cadastre novos canais em `Configurações > Canais`:
- Loja Física
- Site/E-commerce
- WhatsApp
- Marketplace
- Representantes

#### Métodos de Pagamento:
Configure em `Financeiro > Métodos`:
- Dinheiro
- Cartão de Crédito
- Cartão de Débito
- PIX
- Boleto
- Parcelamento

## 📊 Exemplos de Uso

### Cenário 1: Venda com Teste de Produto
1. Cliente solicita teste de raquete
2. Criação de empréstimo com prazo de 3 dias
3. Cliente testa e decide comprar
4. Conversão direta do empréstimo em venda
5. Baixa automática no estoque

### Cenário 2: Trade-in com Nova Compra
1. Cliente traz raquete usada
2. Avaliação do produto usado
3. Criação de pedido com nova raquete
4. Aplicação do valor de trade-in como desconto
5. Finalização da venda

### Cenário 3: Análise de Performance
1. Acesso ao dashboard financeiro
2. Filtro por período (último mês)
3. Análise de conversão por vendedor
4. Identificação de oportunidades
5. Definição de metas para próximo período

## 🐛 Problemas Comuns

### 1. Erro de Conexão com Banco
**Solução**: Verifique as configurações em `config/config.php`

### 2. Problemas de Permissão
**Solução**: Configure permissões 755 para pastas e 644 para arquivos

### 3. URLs não Funcionam
**Solução**: Verifique configuração do `.htaccess` e mod_rewrite

### 4. Erro ao Criar Pedidos
**Solução**: Verifique se todos os produtos têm estoque disponível

## 🔄 Atualizações Recentes

### Versão 2.0 (Setembro 2024)
- ✅ **Sistema de Rastreamento de Vendas**: Controle completo de conversão
- ✅ **Correções de Estoque**: Integração total com vendas
- ✅ **Melhorias em Empréstimos**: Funcionalidades de devolução e conversão
- ✅ **Indicadores Financeiros**: Dashboard completo com métricas avançadas
- ✅ **Consulta Pública**: Interface para clientes consultarem pedidos
- ✅ **Sistema de Trade-in**: Avaliação e uso de produtos usados

## 🤝 Contribuição

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é propriedade privada. Todos os direitos reservados.

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema:

- 🐛 **Issues**: [GitHub Issues](https://github.com/yagoaugusto/VIPBT/issues)
- 📧 **Email**: suporte@vipbt.com
- 📱 **WhatsApp**: (11) 9xxxx-xxxx
- 🌐 **Documentação**: [Wiki do Projeto](https://github.com/yagoaugusto/VIPBT/wiki)

### Comunidade
- 💬 **Discussões**: [GitHub Discussions](https://github.com/yagoaugusto/VIPBT/discussions)
- 📚 **Tutoriais**: Acesse a pasta `/docs` para tutoriais detalhados
- 🎥 **Videos**: Canal no YouTube com demonstrações práticas

---

**VIPBT** - Sistema de Gestão para Beach Tennis  
Desenvolvido com ❤️ para impulsionar seu negócio