<?php
// Test script para verificar a funcionalidade de fulfillment/expedição
// Execute este script após aplicar as correções para testar se tudo está funcionando

// Configuração do teste
require_once 'config/config.php';
require_once 'core/Database.php';

use core\Database;

echo "<h2>Teste de Funcionalidade - Fulfillment/Expedição</h2>";

try {
    $db = new Database();
    
    // 1. Verificar se a tabela fulfillments existe
    echo "<h3>1. Verificando estrutura do banco de dados...</h3>";
    
    $db->query("SHOW TABLES LIKE 'fulfillments'");
    $fulfillmentsTable = $db->single();
    
    if($fulfillmentsTable) {
        echo "✅ Tabela 'fulfillments' existe<br>";
        
        // Verificar estrutura da tabela
        $db->query("DESCRIBE fulfillments");
        $columns = $db->resultSet();
        echo "Colunas da tabela fulfillments:<br>";
        foreach($columns as $column) {
            echo "- {$column->Field} ({$column->Type})<br>";
        }
    } else {
        echo "❌ Tabela 'fulfillments' NÃO existe. Execute o script fix_fulfillment_issues.sql<br>";
    }
    
    // 2. Verificar se existe coluna status_entrega na tabela orders
    echo "<h3>2. Verificando coluna status_entrega na tabela orders...</h3>";
    
    $db->query("SHOW COLUMNS FROM orders LIKE 'status_entrega'");
    $statusColumn = $db->single();
    
    if($statusColumn) {
        echo "✅ Coluna 'status_entrega' existe na tabela orders<br>";
    } else {
        echo "❌ Coluna 'status_entrega' NÃO existe. Execute o script fix_fulfillment_issues.sql<br>";
    }
    
    // 3. Verificar se existem pedidos para teste
    echo "<h3>3. Verificando pedidos disponíveis...</h3>";
    
    $db->query("SELECT COUNT(*) as total FROM orders");
    $orderCount = $db->single();
    
    echo "Total de pedidos: {$orderCount->total}<br>";
    
    if($orderCount->total > 0) {
        $db->query("SELECT id, public_code, total FROM orders ORDER BY id DESC LIMIT 5");
        $orders = $db->resultSet();
        
        echo "Últimos pedidos:<br>";
        foreach($orders as $order) {
            echo "- ID: {$order->id}, Código: {$order->public_code}, Total: R$ " . number_format($order->total, 2, ',', '.') . "<br>";
        }
    }
    
    // 4. Testar inserção de fulfillment
    echo "<h3>4. Testando inserção de fulfillment...</h3>";
    
    if($orderCount->total > 0) {
        // Pegar o primeiro pedido
        $db->query("SELECT id FROM orders LIMIT 1");
        $testOrder = $db->single();
        
        // Tentar inserir um fulfillment de teste
        $testData = [
            'order_id' => $testOrder->id,
            'status' => 'preparando',
            'transportadora' => 'Correios',
            'codigo_rastreio' => 'TEST123456',
            'enviado_em' => null,
            'entregue_em' => null,
            'observacoes' => 'Teste automatizado'
        ];
        
        try {
            $db->beginTransaction();
            
            $db->query("INSERT INTO fulfillments (order_id, status, transportadora, codigo_rastreio, enviado_em, entregue_em, observacoes) VALUES (:order_id, :status, :transportadora, :codigo_rastreio, :enviado_em, :entregue_em, :observacoes)");
            $db->bind(':order_id', $testData['order_id']);
            $db->bind(':status', $testData['status']);
            $db->bind(':transportadora', $testData['transportadora']);
            $db->bind(':codigo_rastreio', $testData['codigo_rastreio']);
            $db->bind(':enviado_em', $testData['enviado_em']);
            $db->bind(':entregue_em', $testData['entregue_em']);
            $db->bind(':observacoes', $testData['observacoes']);
            $db->execute();
            
            $fulfillmentId = $db->lastInsertId();
            
            // Rollback para não deixar dados de teste
            $db->rollBack();
            
            echo "✅ Teste de inserção de fulfillment PASSOU (ID seria: {$fulfillmentId})<br>";
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "❌ Teste de inserção FALHOU: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "⚠️ Nenhum pedido disponível para teste<br>";
    }
    
    echo "<h3>5. Resultado do Teste</h3>";
    echo "✅ Teste concluído. Verifique os resultados acima.<br>";
    
} catch (Exception $e) {
    echo "❌ Erro durante o teste: " . $e->getMessage() . "<br>";
}

echo "<br><a href='orders'>← Voltar para Pedidos</a>";
?>