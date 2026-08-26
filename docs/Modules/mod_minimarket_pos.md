# Domain Module: Minimarket & POS

**Namespace**: `App\Domains\Minimarket`
**Path**: `app/Domains/Minimarket/`

Domain specification for Point of Sale, Cashier Shifts, Product Inventory, Stock Movements, and Member Points.

---

## 📁 Domain Structure

```text
app/Domains/Minimarket/
├── Models/
│   ├── Product.php              ← SKU, barcode, stock, pricing
│   ├── Category.php             ← Product category taxonomy
│   ├── Transaction.php          ← Sales invoice, payment method
│   ├── TransactionItem.php      ← Line items per sale
│   ├── CashierShift.php         ← Shift opening/closing balance
│   ├── StockMovement.php        ← Stock in/out/adjustment audit log
│   ├── RestockRequest.php       ← Inventory restock orders
│   ├── MemberMinimarket.php     ← Retail member metadata
│   ├── MemberPointsHistory.php  ← Points earned/redeemed log
│   └── AuditRetailProductMapping.php ← Retail audit mapping rules
├── Actions/
│   (Future: AddStockAction, ReduceStockAction, OpenCashierShiftAction, etc.)
├── Services/
│   └── POSCheckoutService.php   ← Full POS checkout flow (multi-payment, stock, consignment)
└── Enums/
    ├── PaymentMethod.php        ← CASH, TRANSFER, CREDIT, SUKARELA
    ├── ProductStatus.php        ← ACTIVE, INACTIVE, SEASONAL
    ├── TransactionStatus.php    ← PENDING, COMPLETED, CANCELLED
    ├── TransactionType.php      ← SALE, PURCHASE, RETURN, INCOME, EXPENSE
    └── OwnershipType.php        ← TOKO, TITIPAN, SUPPLIER
```

---

## 📌 Core Business Logic

### 1. POS Checkout Transaction
* **Service**: `POSCheckoutService` handles end-to-end checkout
* **Supports**: CASH, TRANSFER, CREDIT, SUKARELA payment methods
* **Auto-creates**: StockMovement audit records, Member Points, consignment allocation

### 2. Cashier Shift Management
* **Workflow**: Shift Open → Register Sales → Shift Close (reconcile cash drawer)
* **Rules**: Cashier must have active `CashierShift` before creating transactions

### 3. Inventory & Stock
* **Movement Types**: `in` (restock), `out` (shrinkage), `sale` (POS), `adjustment` (stock opname)

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[schema_summary|Database Schema]]
- [[mod_supplier|Supplier & Consignment]]
