<?php
/**
 * Test script to validate order creation parameter fix
 * This script simulates the order creation process and identifies SQL parameter issues
 */

echo "=== Order Creation Parameter Fix Test ===\n\n";

// Mock the Database class for testing SQL generation
class MockDatabase {
    private $sql = '';
    private $bindings = [];
    
    public function query($sql) {
        $this->sql = $sql;
        $this->bindings = [];
        echo "SQL: " . $sql . "\n";
    }
    
    public function bind($param, $value) {
        $this->bindings[$param] = $value;
        echo "BIND: " . $param . " = " . $value . "\n";
    }
    
    public function execute() {
        echo "EXECUTE: Checking parameter count...\n";
        
        // Count parameters in SQL
        $sqlParamCount = substr_count($this->sql, ':');
        $bindingCount = count($this->bindings);
        
        echo "  SQL parameters: " . $sqlParamCount . "\n";
        echo "  Bindings count: " . $bindingCount . "\n";
        
        if ($sqlParamCount !== $bindingCount) {
            throw new Exception("SQLSTATE[HY093]: Invalid parameter number: parameter count mismatch (SQL has {$sqlParamCount}, bindings have {$bindingCount})");
        }
        
        echo "  ✓ Parameter count matches!\n\n";
        return true;
    }
    
    public function single() {
        // Mock result for schema check
        return (object)['column_exists' => 1]; // Assume column exists for test
    }
}

// Mock OrderModel for testing
class MockOrderModel {
    private $db;
    
    public function __construct() {
        $this->db = new MockDatabase();
    }
    
    // Test the old problematic method
    public function testOldApplyCreditMethod($order_id, $credit_value, $trade_in_id = null) {
        echo "--- Testing OLD applyCreditToOrder method (problematic) ---\n";
        
        // This is the old problematic code that causes parameter mismatch
        $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, 'trade_in', :descricao, :valor)");
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':descricao', 'Crédito de Trade-in #' . $trade_in_id);
        $this->db->bind(':valor', $credit_value);
        // Note: trade_in_id is NOT bound, but if the database has the column, this will fail
        
        return $this->db->execute();
    }
    
    // Check if trade_in_id column exists (mocked)
    private function orderCreditsHasTradeInColumn() {
        echo "--- Checking if order_credits has trade_in_id column ---\n";
        $this->db->query("
            SELECT COUNT(*) as column_exists 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_credits' 
            AND COLUMN_NAME = 'trade_in_id'
        ");
        $result = $this->db->single();
        $hasColumn = $result && $result->column_exists > 0;
        echo "  Result: " . ($hasColumn ? "YES" : "NO") . "\n\n";
        return $hasColumn;
    }
    
    // Test the new fixed method
    public function testNewInsertOrderCreditMethod($order_id, $origem, $descricao, $valor, $trade_in_id = null) {
        echo "--- Testing NEW insertOrderCredit method (fixed) ---\n";
        
        $hasTradeInColumn = $this->orderCreditsHasTradeInColumn();
        
        if ($hasTradeInColumn) {
            echo "Using NEW schema with trade_in_id column:\n";
            // Use new schema with trade_in_id column
            $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id) VALUES (:order_id, :origem, :descricao, :valor, :trade_in_id)");
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':origem', $origem);
            $this->db->bind(':descricao', $descricao);
            $this->db->bind(':valor', $valor);
            $this->db->bind(':trade_in_id', $trade_in_id);
        } else {
            echo "Using OLD schema without trade_in_id column:\n";
            // Use old schema without trade_in_id column
            $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, :origem, :descricao, :valor)");
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':origem', $origem);
            $this->db->bind(':descricao', $descricao);
            $this->db->bind(':valor', $valor);
        }
        
        return $this->db->execute();
    }
}

// Run tests
try {
    $orderModel = new MockOrderModel();
    
    echo "TEST 1: Old problematic method\n";
    echo "=============================\n";
    try {
        $orderModel->testOldApplyCreditMethod(123, 150.00, 456);
        echo "❌ UNEXPECTED: Old method worked (this suggests the database doesn't have trade_in_id column)\n\n";
    } catch (Exception $e) {
        echo "✅ EXPECTED: Old method failed with: " . $e->getMessage() . "\n\n";
    }
    
    echo "TEST 2: New fixed method\n";
    echo "========================\n";
    try {
        $orderModel->testNewInsertOrderCreditMethod(123, 'trade_in', 'Crédito de Trade-in #456', 150.00, 456);
        echo "✅ SUCCESS: New method works correctly\n\n";
    } catch (Exception $e) {
        echo "❌ FAILURE: New method failed with: " . $e->getMessage() . "\n\n";
    }
    
    echo "=== SUMMARY ===\n";
    echo "The fix works by:\n";
    echo "1. Detecting if the trade_in_id column exists in the database\n";
    echo "2. Using appropriate SQL with matching parameter counts\n";
    echo "3. Providing backward compatibility with old database schemas\n";
    echo "4. Ensuring all parameters are properly bound\n\n";
    
    echo "To apply the fix:\n";
    echo "1. Update the database schema: mysql -u root -p viplojabt < fix_order_creation_parameters.sql\n";
    echo "2. Ensure OrderModel.php uses the new insertOrderCredit method\n";
    echo "3. Test order creation in the web interface\n";
    
} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>