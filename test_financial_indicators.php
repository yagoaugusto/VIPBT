<?php
// Test script for Financial Indicators Dashboard
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Financial Indicators Dashboard Test ===\n\n";

// Set up the test environment
require_once 'config/config.php';
require_once 'core/Database.php';
require_once 'models/FinancialIndicatorsModel.php';

try {
    echo "1. Testing FinancialIndicatorsModel instantiation...\n";
    $model = new FinancialIndicatorsModel();
    echo "✓ Model instantiated successfully\n\n";

    echo "2. Testing getFinancialOverview method...\n";
    $overview = $model->getFinancialOverview();
    echo "✓ Financial overview retrieved successfully\n";
    echo "   - Total Orders: " . ($overview->total_orders ?? 0) . "\n";
    echo "   - Total Sales: " . ($overview->total_sales ?? 0) . "\n";
    echo "   - Amount Received: R$ " . number_format($overview->amount_received ?? 0, 2, ',', '.') . "\n";
    echo "   - Amount to Receive: R$ " . number_format($overview->amount_to_receive ?? 0, 2, ',', '.') . "\n\n";

    echo "3. Testing getMostSoldProducts method...\n";
    $products = $model->getMostSoldProducts(null, null, 5);
    echo "✓ Most sold products retrieved successfully\n";
    echo "   - Number of products found: " . count($products) . "\n";
    
    if (!empty($products)) {
        echo "   - Top product: " . $products[0]->product_name . " (Qty: " . $products[0]->total_quantity . ")\n";
    }
    echo "\n";

    echo "4. Testing getTopCustomersByPurchases method...\n";
    $customers = $model->getTopCustomersByPurchases(null, null, 5);
    echo "✓ Top customers by purchases retrieved successfully\n";
    echo "   - Number of customers found: " . count($customers) . "\n";
    
    if (!empty($customers)) {
        echo "   - Top customer: " . $customers[0]->customer_name . " (Spent: R$ " . number_format($customers[0]->total_spent, 2, ',', '.') . ")\n";
    }
    echo "\n";

    echo "5. Testing getTopCustomersByLoans method...\n";
    $loanCustomers = $model->getTopCustomersByLoans(null, null, 5);
    echo "✓ Top customers by loans retrieved successfully\n";
    echo "   - Number of customers found: " . count($loanCustomers) . "\n";
    
    if (!empty($loanCustomers)) {
        echo "   - Top customer: " . $loanCustomers[0]->customer_name . " (Loans: " . $loanCustomers[0]->total_loans . ")\n";
    }
    echo "\n";

    echo "6. Testing getSalesChannelStats method...\n";
    $channels = $model->getSalesChannelStats();
    echo "✓ Sales channel stats retrieved successfully\n";
    echo "   - Number of channels found: " . count($channels) . "\n";
    echo "\n";

    echo "7. Testing getPaymentMethodStats method...\n";
    $payments = $model->getPaymentMethodStats();
    echo "✓ Payment method stats retrieved successfully\n";
    echo "   - Number of payment methods found: " . count($payments) . "\n";
    echo "\n";

    echo "8. Testing getMonthlyRevenueData method...\n";
    $monthlyData = $model->getMonthlyRevenueData();
    echo "✓ Monthly revenue data retrieved successfully\n";
    echo "   - Number of months found: " . count($monthlyData) . "\n";
    echo "\n";

    echo "=== ALL TESTS PASSED ===\n";
    echo "The Financial Indicators Dashboard is working correctly!\n";

} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>