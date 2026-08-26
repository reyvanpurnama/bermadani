# Domain Module: Supplier & Consignment

**Namespace**: `App\Domains\Supplier`
**Path**: `app/Domains/Supplier/`

Domain specification for Vendor Management, Consignment Goods Tracking, Supplier Notifications, and Payout Allocations.

---

## 📁 Domain Structure

```text
app/Domains/Supplier/
├── Models/
│   ├── Supplier.php                 ← Supplier profiles, contact, payment terms
│   ├── ConsignmentBatch.php         ← Batch of goods received on consignment
│   ├── ConsignmentItem.php          ← Individual items within a batch
│   ├── ConsignmentItemCount.php     ← Stock check audit counts
│   ├── SupplierPayout.php           ← Payout statements for sold items
│   ├── SupplierPayoutAllocation.php ← Itemized payout distribution
│   └── SupplierNotification.php     ← Automated alerts to suppliers
├── Actions/
│   (Future: ApproveSupplierAction, SuspendSupplierAction, etc.)
├── Services/
│   ├── SupplierService.php          ← Registration & compliance management
│   └── SupplierSalesService.php     ← Multi-source sales reconciliation
└── Enums/
    ├── SupplierStatus.php           ← PENDING, APPROVED, ACTIVE, SUSPENDED, REJECTED
    └── RegistrationPaymentStatus.php ← UNPAID, PENDING_VERIFICATION, VERIFIED, REJECTED
```

---

## 📌 Core Business Logic

### 1. Consignment System
* Suppliers deliver products under a batch (`ConsignmentBatch`). Payment issued only after items are sold.
* `ConsignmentItemCount` used for periodic stock audit.

### 2. Supplier Payouts
* POS sales log sold consignment items → Period end generates `SupplierPayout` → Allocations linked → Payment verified & notification sent.

### 3. Restock Workflow
* Low stock detected → `RestockRequest` triggered → Supplier notified → Goods received into new `ConsignmentBatch`.

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[schema_summary|Database Schema]]
- [[mod_minimarket_pos|Minimarket POS]]
