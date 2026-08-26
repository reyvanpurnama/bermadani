# 01_ARCHITECTURE_GUIDE: Modular Monolith Blueprint

This document outlines the **active architecture** of **Koperasi Bermadani** after refactoring from a flat Laravel structure to a **Modular Monolith** with Domain-Driven Design.

---

## 🎯 Why Modular Monolith over Microservices?

1. **Transactional Integrity**: Koperasi transactions (e.g. POS cashier sale deducting member savings and creating accounting journal entries) require ACID database guarantees. Monolith allows simple `DB::transaction()`.
2. **Low Maintenance**: Single deployment target for small/medium cooperative workloads.
3. **High Token Efficiency**: Code is modularized into small, isolated action classes (50-100 lines each), making it extremely easy to feed single classes to AI models.

---

## 🏗️ Active Directory Structure

```text
app/
├── Domains/
│   ├── Koperasi/                       ← Simpan Pinjam & Keanggotaan
│   │   ├── Actions/                    (PaySimpananAction, ApproveLoanAction, etc.)
│   │   ├── Models/                     (Member, Loan, Saving, RatSession, etc.)
│   │   ├── Services/                   (MemberService, ShuCalculationService, SimpananPaymentService)
│   │   └── Enums/
│   ├── Minimarket/                     ← POS Retail & Inventory
│   │   ├── Actions/
│   │   ├── Models/                     (Product, Transaction, CashierShift, StockMovement, etc.)
│   │   ├── Services/                   (POSCheckoutService)
│   │   └── Enums/                      (PaymentMethod, ProductStatus, TransactionStatus, etc.)
│   ├── Supplier/                       ← Vendor & Konsinyasi
│   │   ├── Actions/
│   │   ├── Models/                     (Supplier, ConsignmentBatch, SupplierPayout, etc.)
│   │   ├── Services/                   (SupplierService, SupplierSalesService)
│   │   └── Enums/                      (SupplierStatus, RegistrationPaymentStatus)
│   └── Accounting/                     ← Finance, Audit, Payroll
│       ├── Actions/
│       ├── Models/                     (FinancialTransaction, BankTransaction, WorkLog, etc.)
│       └── Services/                   (RatDetailService)
├── Shared/                             ← Cross-domain shared utilities
│   ├── Models/                         (User, ActivityLog)
│   └── Enums/                          (UserRole)
├── Http/Controllers/                   ← Slim controllers delegating to Services/Actions
├── Livewire/                           ← 72 components (UI layer, not yet domain-organized)
└── Console/Commands/                   ← 16 CLI commands
```

---

## 🔄 Action Class Pattern

Business logic extracted from fat models into single-responsibility Action classes:

### Koperasi Domain Actions

| Action Class | Responsibility |
|---|---|
| `PaySimpananAction` | Process simpanan deposit/withdrawal |
| `PayWithSukarelaAction` | Validate & deduct simpanan sukarela for POS payment |
| `AddMemberPointsAction` | Award loyalty points + trigger tier recalc |
| `RedeemPointsAction` | Validate & deduct loyalty points |
| `UpdateMemberTierAction` | Recalculate tier (Bronze/Silver/Gold/Platinum) |
| `ApproveLoanAction` | Transition loan to ACTIVE + stamp approval |
| `RejectLoanAction` | Transition loan to REJECTED |
| `ProcessLoanPaymentAction` | Record installment + update remaining balance |
| `DisburseShuAction` | Create FinancialTransaction + mark SHU as disbursed |

### Example Usage

```php
use App\Domains\Koperasi\Actions\ApproveLoanAction;

class LoanController extends Controller
{
    public function approve(Loan $loan, ApproveLoanAction $action)
    {
        $action->execute($loan, auth()->id());
        return redirect()->back()->with('success', 'Pinjaman disetujui.');
    }
}
```

---

## ♻️ Backward Compatibility

Alias classes in `app/Models/` and `app/Services/` ensure **all existing imports work without changes**:

```php
// app/Models/Member.php (alias)
namespace App\Models;
class Member extends \App\Domains\Koperasi\Models\Member {}
```

This means:
- ✅ All 72 Livewire components still work (`use App\Models\Member`)
- ✅ All Controllers still work
- ✅ All Console Commands still work
- ✅ New code can use `use App\Domains\Koperasi\Models\Member` directly

---

## 🔗 Related Notes
- [[00_OVERVIEW|Back to System Overview]]
- [[schema_summary|Database Schemas]]
- [[Coding_Standards|Coding Standards & AI Guidelines]]
- [[Refactoring_Log|Refactoring Changelog]]
