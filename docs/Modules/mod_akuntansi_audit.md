# Domain Module: Accounting & Audit

**Namespace**: `App\Domains\Accounting`
**Path**: `app/Domains/Accounting/`

Domain specification for Financial Ledger Transactions, Snapshots, Bank Reconciliation, and Import Rules.

---

## 📁 Domain Structure

```text
app/Domains/Accounting/
├── Models/
│   ├── FinancialTransaction.php      ← General ledger transaction records
│   ├── FinancialReportSnapshot.php   ← Archived monthly/yearly summaries
│   ├── BankTransaction.php           ← Bank statement import logs
│   ├── AuditBankImport.php           ← Batch metadata for bank imports
│   ├── AuditBankCategoryRule.php     ← Auto-categorization rules for reconciliation
│   ├── AuditLoanImport.php           ← Batch metadata for loan audits
│   └── WorkLog.php                   ← Staff/Developer work hours & payroll
├── Actions/
│   (Future: ReconcileBankImportAction, GenerateSnapshotAction, etc.)
└── Services/
    └── RatDetailService.php          ← RAT financial statement calculation engine
```

---

## 📌 Core Business Logic

### 1. Financial Ledger & Snapshots
* `FinancialTransaction` records every income/expense across POS, Savings, Loans, Payouts
* Monthly snapshots archived in `FinancialReportSnapshot`

### 2. Bank Reconciliation
* Bank statements (CSV) imported via `AuditBankImport`
* Auto-categorization via `AuditBankCategoryRule` (regex pattern matching on descriptions)

### 3. RAT Financial Statements
* `RatDetailService` aggregates multi-year comparative data, merges ledger + manual entries

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[schema_summary|Database Schema]]
- [[mod_koperasi|Koperasi & Simpan Pinjam]]
