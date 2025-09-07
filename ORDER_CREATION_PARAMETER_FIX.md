# Fix for Order Creation SQL Parameter Error (SQLSTATE[HY093])

## Problem
When creating orders at `http://localhost/VIPLOJABT/orders/add`, users encounter the error:
```
Erro ao criar o pedido: SQLSTATE[HY093]: Invalid parameter number.
```

## Root Cause
The error was caused by inconsistent SQL queries for the `order_credits` table:

1. **Inconsistent INSERT statements**: Two different methods used different column sets:
   - `addOrder()` method used: `INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id)`
   - `applyCreditToOrder()` method used: `INSERT INTO order_credits (order_id, origem, descricao, valor)` (missing `trade_in_id`)

2. **Database schema mismatch**: The `trade_in_id` column was added in Sprint 8 but not all environments may have this update applied.

## Solution

### 1. Fixed SQL Parameter Consistency
**File**: `models/OrderModel.php`

- **Before**:
```php
$this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, 'trade_in', :descricao, :valor)");
$this->db->bind(':order_id', $order_id);
$this->db->bind(':descricao', $description);
$this->db->bind(':valor', $credit_value);
```

- **After**:
```php
$this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id) VALUES (:order_id, 'trade_in', :descricao, :valor, :trade_in_id)");
$this->db->bind(':order_id', $order_id);
$this->db->bind(':descricao', $description);
$this->db->bind(':valor', $credit_value);
$this->db->bind(':trade_in_id', $trade_in_id);
```

### 2. Added Database Schema Validation
Added `checkRequiredTableColumns()` method to verify database schema before order creation:
- Checks for `trade_in_id` column in `order_credits` table
- Checks for `total` column in `orders` table
- Throws descriptive error messages if columns are missing

### 3. Database Update Script
**File**: `fix_order_creation_parameters.sql`

Ensures required columns exist in the database:
- Adds `trade_in_id` column to `order_credits` table if missing
- Adds `total` column to `orders` table if missing
- Adds proper foreign key constraints

## How to Apply the Fix

### 1. Database Updates
Run the database update script:
```sql
mysql -u root -p viplojabt < fix_order_creation_parameters.sql
```

### 2. Code Updates
The PHP code has been updated in:
- `models/OrderModel.php`

### 3. Verification
Use the test script to verify the fix:
```bash
php /tmp/test_order_creation.php
```

## Testing the Fix

1. **Basic Order Creation**:
   - Navigate to `/orders/add`
   - Fill in customer, seller, channel, and add items
   - Submit the form
   - Should create order successfully without parameter errors

2. **Order with Trade-ins**:
   - Create order with trade-in credits applied
   - Verify credits are properly recorded in `order_credits` table

3. **Error Handling**:
   - If database schema is incomplete, should show descriptive error message instead of cryptic SQL error

## Benefits

1. **Consistent SQL Queries**: All `order_credits` insertions now use the same column structure
2. **Better Error Messages**: Clear indication when database schema needs updating
3. **Robust Operation**: System validates database schema before attempting operations
4. **Backward Compatibility**: Works with both old and new database schemas

## Files Modified

- `models/OrderModel.php` - Fixed parameter consistency and added schema validation
- `fix_order_creation_parameters.sql` - Database update script (new file)

## Prevention

To prevent similar issues in the future:
1. Always run database migration scripts when updating environments
2. Use consistent SQL query structures across all methods
3. Add schema validation for critical operations
4. Test parameter counts in development environments