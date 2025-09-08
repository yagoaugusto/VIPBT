<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?> - VIP LOJA BT</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .consultation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .timeline {
            position: relative;
            margin: 2rem 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 25px;
            width: 2px;
            height: 100%;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 60px;
        }
        
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            z-index: 1;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .header-brand {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-bottom: 0;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .item-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: box-shadow 0.2s ease;
        }
        
        .item-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .consultation-form {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .status-preparando { background-color: #ffc107 !important; }
        .status-enviado { background-color: #0dcaf0 !important; }
        .status-entregue { background-color: #198754 !important; }
        
        .tracking-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #2196f3;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .btn-copy {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-copy:hover {
            background-color: #0056b3 !important;
        }
        
        @media (max-width: 768px) {
            .header-brand {
                padding: 1rem 0;
            }
            
            .consultation-card {
                margin: 1rem;
            }
            
            .timeline-item {
                padding-left: 40px;
            }
            
            .timeline-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header-brand">
        <h1><i class="fas fa-shopping-bag"></i> VIP LOJA BT</h1>
        <p class="mb-0">Consulta Pública de Pedidos</p>
    </div>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="consultation-card">
                    <div class="card-body p-4">
                        <?php if(!$data['public_code']): ?>
                            <!-- Formulário de busca -->
                            <div class="text-center mb-4">
                                <h3><i class="fas fa-search"></i> Consulte seu Pedido</h3>
                                <p class="text-muted">Digite o código do seu pedido para acompanhar o status</p>
                            </div>
                            
                            <div class="consultation-form">
                                <form action="" method="GET" class="mb-4">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                        <input type="text" class="form-control" name="code" placeholder="Ex: BT-9X3Q7L" required autocomplete="off">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Consultar
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt"></i> Suas informações estão protegidas e seguras
                                    </small>
                                </div>
                            </div>
                            
                        <?php elseif($data['error']): ?>
                            <!-- Erro -->
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h4>Ops! Algo deu errado</h4>
                                <p class="mb-0"><?php echo $data['error']; ?></p>
                            </div>
                            
                            <div class="text-center">
                                <a href="../consulta/" class="btn btn-primary">
                                    <i class="fas fa-arrow-left"></i> Tentar Novamente
                                </a>
                            </div>
                            
                        <?php elseif($data['order']): ?>
                            <!-- Resultado da consulta -->
                            <div class="order-summary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><i class="fas fa-receipt"></i> Pedido <?php echo $data['order']->public_code; ?></h4>
                                        <p class="mb-1"><strong>Cliente:</strong> <?php echo $data['order']->customer_nome; ?></p>
                                        <p class="mb-1"><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($data['order']->data)); ?></p>
                                        <p class="mb-0"><strong>Canal:</strong> <?php echo $data['order']->channel_nome; ?></p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <h5 class="text-primary mb-2">Total: R$ <?php echo number_format($data['order']->total, 2, ',', '.'); ?></h5>
                                        
                                        <?php 
                                        $statusColor = 'secondary';
                                        $statusText = 'Processando';
                                        if($data['fulfillment']) {
                                            switch($data['fulfillment']->status) {
                                                case 'preparando': $statusColor = 'warning'; $statusText = 'Preparando'; break;
                                                case 'enviado': $statusColor = 'info'; $statusText = 'Enviado'; break;
                                                case 'entregue': $statusColor = 'success'; $statusText = 'Entregue'; break;
                                            }
                                        }
                                        ?>
                                        <span class="status-badge bg-<?php echo $statusColor; ?> text-white">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline do Pedido -->
                            <?php if(!empty($data['timeline'])): ?>
                                <h5><i class="fas fa-timeline"></i> Acompanhamento do Pedido</h5>
                                <div class="timeline">
                                    <?php foreach($data['timeline'] as $item): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-icon bg-<?php echo $item['color']; ?>">
                                                <i class="fas <?php echo $item['icon']; ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1"><?php echo $item['status']; ?></h6>
                                                <p class="mb-1"><?php echo $item['description']; ?></p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($item['date'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Itens do Pedido -->
                            <?php if(!empty($data['order_items'])): ?>
                                <h5><i class="fas fa-box-open"></i> Itens do Pedido</h5>
                                <?php foreach($data['order_items'] as $item): ?>
                                    <div class="item-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?php echo $item->product_nome; ?></h6>
                                                <small class="text-muted">SKU: <?php echo $item->sku; ?></small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <span class="badge bg-light text-dark">Qtd: <?php echo $item->qtd; ?></span>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <small>R$ <?php echo number_format($item->preco_unit, 2, ',', '.'); ?></small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <strong>R$ <?php echo number_format(($item->preco_unit * $item->qtd) - $item->desconto, 2, ',', '.'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Código de Rastreio -->
                            <?php if($data['fulfillment'] && $data['fulfillment']->codigo_rastreio): ?>
                                <div class="tracking-info mt-3">
                                    <h6><i class="fas fa-truck"></i> Informações de Rastreamento</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Transportadora:</strong> <?php echo $data['fulfillment']->transportadora; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Código de Rastreio:</strong></p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2 fs-6"><?php echo $data['fulfillment']->codigo_rastreio; ?></span>
                                                <button class="btn btn-sm btn-outline-primary btn-copy" onclick="copyTrackingCode('<?php echo $data['fulfillment']->codigo_rastreio; ?>')" title="Copiar código">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($data['fulfillment']->enviado_em): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Enviado em: <?php echo date('d/m/Y H:i', strtotime($data['fulfillment']->enviado_em)); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="text-center mt-4">
                                <a href="../consulta/" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Consultar Outro Pedido
                                </a>
                            </div>
                            
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white">
                        © <?php echo date('Y'); ?> VIP LOJA BT - Todos os direitos reservados
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to copy tracking code to clipboard
        function copyTrackingCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success feedback
                const btn = event.target.closest('.btn-copy');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function(err) {
                console.error('Erro ao copiar: ', err);
            });
        }

        // Auto-focus on search input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="code"]');
            if(searchInput) {
                searchInput.focus();
            }
        });

        // Format input code as user types
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.querySelector('input[name="code"]');
            if(codeInput) {
                codeInput.addEventListener('input', function() {
                    // Convert to uppercase and add BT- prefix if not present
                    let value = this.value.toUpperCase();
                    if(value && !value.startsWith('BT-') && value.length > 2) {
                        value = 'BT-' + value.replace('BT-', '');
                    }
                    this.value = value;
                });
            }
        });
    </script>
</body>
</html>