# Database Schema Summary

This document categorizes all 36 Eloquent models in **Koperasi Bermadani** into functional database domains.

---

## 👥 Member & User Core Domain

| Model | Table Name | Key Attributes & Description |
|---|---|---|
| `User` | `users` | Auth users, roles (admin, kasir, pengurus), email, password |
| `Member` | `members` | Primary cooperative member identity, NIK, phone, points, status |
| `MemberKoperasi` | `member_koperasi` | Cooperative-specific membership details & status |
| `MemberMinimarket` | `member_minimarket` | Retail/minimarket-specific member metadata |
| `MemberPointsHistory` | `member_points_histories` | Audit log of points earned/redeemed at POS |
| `ActivityLog` | `activity_logs` | User action tracking log |
| `WorkLog` | `work_logs` | Staff/Employee work activity tracking |

---

## 💰 Koperasi & Simpan Pinjam Domain

| Model | Table Name | Key Attributes & Description |
|---|---|---|
| `Saving` | `savings` | Master savings balance (Pokok, Wajib, Sukarela) |
| `SimpananTransaction` | `simpanan_transactions` | Deposit/withdrawal history with type & status |
| `SimpananPayment` | `simpanan_payments` | Payment verification records for simpanan |
| `Loan` | `loans` | Active loans, interest rate, duration, status |
| `LoanPayment` | `loan_payments` | Loan installment payments log |
| `MemberShuDistribution` | `member_shu_distributions` | Annual SHU calculation & distribution results |
| `RatSession` | `rat_sessions` | Annual Member Assembly (RAT) session records |
| `RatManualEntry` | `rat_manual_entries` | Manual adjustments for RAT financial reports |

---

## 🛒 Minimarket & POS Domain

| Model | Table Name | Key Attributes & Description |
|---|---|---|
| `Product` | `products` | SKU, barcode, title, cost_price, selling_price, stock |
| `Category` | `categories` | Product category taxonomy |
| `Transaction` | `transactions` | Sales invoice, total_amount, payment_method, cashier_id |
| `TransactionItem` | `transaction_items` | Line items per sale (product_id, qty, price, subtotal) |
| `CashierShift` | `cashier_shifts` | Cashier shift opening/closing balance, total cash |
| `StockMovement` | `stock_movements` | Stock in/out/adjustment log |
| `RestockRequest` | `restock_requests` | Inventory restock orders from suppliers/warehouse |

---

## 🚛 Supplier & Consignment Domain

| Model | Table Name | Key Attributes & Description |
|---|---|---|
| `Supplier` | `suppliers` | Supplier profiles, contact info, payment terms |
| `ConsignmentBatch` | `consignment_batches` | Batch of goods received on consignment |
| `ConsignmentItem` | `consignment_items` | Individual items within a consignment batch |
| `ConsignmentItemCount` | `consignment_item_counts` | Stock check audit counts for consignment |
| `SupplierNotification` | `supplier_notifications` | Automated alerts sent to suppliers |
| `SupplierPayout` | `supplier_payouts` | Payout statements for sold consignment items |
| `SupplierPayoutAllocation` | `supplier_payout_allocations` | Itemized distribution of payout funds |

---

## 📊 Accounting & Audit Domain

| Model | Table Name | Key Attributes & Description |
|---|---|---|
| `FinancialTransaction` | `financial_transactions` | General ledger transaction records |
| `FinancialReportSnapshot` | `financial_report_snapshots` | Archived monthly/yearly financial summaries |
| `BankTransaction` | `bank_transactions` | Bank statement transaction import logs |
| `AuditBankImport` | `audit_bank_imports` | Batch metadata for imported bank statements |
| `AuditLoanImport` | `audit_loan_imports` | Batch metadata for imported loan audits |
| `AuditBankCategoryRule` | `audit_bank_category_rules` | Auto-categorization rules for bank reconciliation |
| `AuditRetailProductMapping` | `audit_retail_product_mappings` | Mapping rules for retail inventory auditing |

---

## 🔗 Related Notes
- [[ERD_relationships|Entity Relationships & Foreign Keys]]
- [[00_OVERVIEW|System Overview]]
