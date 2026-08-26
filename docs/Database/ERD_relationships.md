# ERD & Key Entity Relationships

High-level overview of primary relationships across core tables in **Koperasi Bermadani**.

---

## 🔗 Key Foreign Key Mappings

```mermaid
erDiagram
    USERS ||--o| MEMBERS : "1-to-1 Profile"
    MEMBERS ||--o{ SAVINGS : "has savings"
    MEMBERS ||--o{ LOANS : "has loans"
    MEMBERS ||--o{ TRANSACTIONS : "buys at POS"
    
    MEMBERS ||--o{ MEMBER_SHU_DISTRIBUTIONS : "receives SHU"
    
    SAVINGS ||--o{ SIMPANAN_TRANSACTIONS : "logs deposit/withdrawal"
    LOANS ||--o{ LOAN_PAYMENTS : "logs repayment"
    
    CASHIER_SHIFTS ||--o{ TRANSACTIONS : "groups POS sales"
    TRANSACTIONS ||--o{ TRANSACTION_ITEMS : "contains items"
    PRODUCTS ||--o{ TRANSACTION_ITEMS : "sold as item"
    CATEGORIES ||--o{ PRODUCTS : "categorizes"
    
    SUPPLIERS ||--o{ CONSIGNMENT_BATCHES : "supplies batch"
    CONSIGNMENT_BATCHES ||--o{ CONSIGNMENT_ITEMS : "contains consignment products"
    SUPPLIERS ||--o{ SUPPLIER_PAYOUTS : "receives payout"
```

---

## 📌 Core Domain Connection Rules

1. **Member -> POS & Simpan Pinjam**:
   - `Member` is the central pivot. A POS transaction linked to a `member_id` automatically accumulates points logged in `MemberPointsHistory`.
   - Savings (`savings`) are divided by type (`simpanan_pokok`, `simpanan_wajib`, `simpanan_sukarela`).

2. **Minimarket POS -> Accounting**:
   - When a `Transaction` completes, `TransactionItem`s deduct `Product` stock via `StockMovement`.
   - Payment totals generate a `FinancialTransaction` ledger entry for bookkeeping.

3. **Consignment & Payouts**:
   - Products from `Supplier` belong to a `ConsignmentBatch`. When sold at POS, payout calculations link `TransactionItem` -> `ConsignmentItem` -> `SupplierPayout`.

---

## 🔗 Related Notes
- [[schema_summary|Database Schema Summary]]
- [[00_OVERVIEW|System Overview]]
