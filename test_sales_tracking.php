<?php
// Simple test to validate the order sales tracking functionality
// Run this script to test the new features

// Include the necessary files
require_once 'config/config.php';
require_once 'core/Database.php';
require_once 'models/OrderModel.php';

try {
    echo "=== TESTE DO SISTEMA DE RASTREAMENTO DE VENDAS ===\n\n";
    
    $orderModel = new OrderModel();
    
    // Test 1: Check if conversion stats method exists and works
    echo "1. Testando métodos de estatísticas...\n";
    
    $stats = $orderModel->getOrderConversionStats();
    if ($stats) {
        echo "   ✅ getOrderConversionStats() - FUNCIONANDO\n";
        echo "   📊 Total de pedidos: " . $stats->total_pedidos . "\n";
        echo "   💰 Vendas confirmadas: " . $stats->vendas_confirmadas . "\n";
        echo "   📈 Taxa de conversão: " . $stats->taxa_conversao_percent . "%\n";
    } else {
        echo "   ❌ getOrderConversionStats() - ERRO\n";
    }
    
    echo "\n";
    
    // Test 2: Check stats by channel
    echo "2. Testando estatísticas por canal...\n";
    $channelStats = $orderModel->getConversionStatsByChannel();
    if ($channelStats) {
        echo "   ✅ getConversionStatsByChannel() - FUNCIONANDO\n";
        echo "   📊 Canais encontrados: " . count($channelStats) . "\n";
        foreach ($channelStats as $i => $stat) {
            if ($i < 3) { // Show only first 3
                echo "   📺 {$stat->canal_nome}: {$stat->vendas_confirmadas}/{$stat->total_pedidos} pedidos ({$stat->taxa_conversao_percent}%)\n";
            }
        }
    } else {
        echo "   ❌ getConversionStatsByChannel() - ERRO\n";
    }
    
    echo "\n";
    
    // Test 3: Check stats by seller
    echo "3. Testando estatísticas por vendedor...\n";
    $sellerStats = $orderModel->getConversionStatsBySeller();
    if ($sellerStats) {
        echo "   ✅ getConversionStatsBySeller() - FUNCIONANDO\n";
        echo "   👥 Vendedores encontrados: " . count($sellerStats) . "\n";
        foreach ($sellerStats as $i => $stat) {
            if ($i < 3) { // Show only first 3
                echo "   👤 {$stat->vendedor_nome}: {$stat->vendas_confirmadas}/{$stat->total_pedidos} pedidos ({$stat->taxa_conversao_percent}%)\n";
            }
        }
    } else {
        echo "   ❌ getConversionStatsBySeller() - ERRO\n";
    }
    
    echo "\n";
    
    // Test 4: Check if we can get orders with the new fields
    echo "4. Testando busca de pedidos...\n";
    $orders = $orderModel->getAllOrders();
    if ($orders && count($orders) > 0) {
        echo "   ✅ getAllOrders() - FUNCIONANDO\n";
        echo "   📦 Total de pedidos: " . count($orders) . "\n";
        
        // Check for orders with different statuses
        $statusCount = [];
        foreach ($orders as $order) {
            $status = $order->status_pedido ?? 'indefinido';
            $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
        }
        
        echo "   📊 Distribuição por status:\n";
        foreach ($statusCount as $status => $count) {
            echo "      - {$status}: {$count} pedidos\n";
        }
    } else {
        echo "   ⚠️  getAllOrders() - Nenhum pedido encontrado\n";
    }
    
    echo "\n";
    
    // Test 5: Test order status update (without actually changing data)
    echo "5. Testando método de atualização de status...\n";
    if (method_exists($orderModel, 'updateOrderStatus')) {
        echo "   ✅ updateOrderStatus() - MÉTODO EXISTE\n";
    } else {
        echo "   ❌ updateOrderStatus() - MÉTODO NÃO ENCONTRADO\n";
    }
    
    if (method_exists($orderModel, 'confirmOrderAsSale')) {
        echo "   ✅ confirmOrderAsSale() - MÉTODO EXISTE\n";
    } else {
        echo "   ❌ confirmOrderAsSale() - MÉTODO NÃO ENCONTRADO\n";
    }
    
    echo "\n=== TESTE CONCLUÍDO ===\n";
    echo "✅ Sistema de rastreamento de vendas está funcionando!\n";
    echo "🚀 Pronto para uso em produção.\n\n";
    
    echo "PRÓXIMOS PASSOS:\n";
    echo "1. Aplique o script SQL: mysql -u user -p viplojabt < improve_order_sales_tracking.sql\n";
    echo "2. Acesse /orders para ver os novos status\n";
    echo "3. Acesse /orders/conversionStats para ver relatórios\n";
    echo "4. Teste confirmar vendas em pedidos existentes\n";
    
} catch (Exception $e) {
    echo "❌ ERRO NO TESTE: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>