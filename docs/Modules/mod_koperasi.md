# Domain Module: Koperasi & Simpan Pinjam

**Namespace**: `App\Domains\Koperasi`
**Path**: `app/Domains/Koperasi/`

Domain specification for Member Savings, Loans, and Annual SHU Distribution.

---

## 📁 Domain Structure

```text
app/Domains/Koperasi/
├── Models/
│   ├── Member.php              ← Central member identity, points, tier, savings balances
│   ├── MemberKoperasi.php      ← Koperasi-specific membership details
│   ├── Saving.php              ← Master savings balance record
│   ├── SimpananTransaction.php ← Deposit/withdrawal history
│   ├── SimpananPayment.php     ← Payment verification records
│   ├── Loan.php                ← Active loans, interest, status
│   ├── LoanPayment.php         ← Loan installment payments
│   ├── MemberShuDistribution.php ← Annual SHU calculation results
│   ├── RatSession.php          ← Annual RAT session records
│   └── RatManualEntry.php      ← Manual RAT adjustments
├── Actions/
│   ├── PaySimpananAction.php        ← Process simpanan deposit/withdrawal
│   ├── PayWithSukarelaAction.php    ← Deduct sukarela for POS payment
│   ├── AddMemberPointsAction.php    ← Award loyalty points + tier recalc
│   ├── RedeemPointsAction.php       ← Validate & deduct points
│   ├── UpdateMemberTierAction.php   ← Recalculate tier level
│   ├── ApproveLoanAction.php        ← Transition loan → ACTIVE
│   ├── RejectLoanAction.php         ← Transition loan → REJECTED
│   ├── ProcessLoanPaymentAction.php ← Record installment payment
│   └── DisburseShuAction.php        ← Create FinancialTransaction + disburse
├── Services/
│   ├── MemberService.php            ← Member lifecycle orchestration
│   ├── ShuCalculationService.php    ← SHU pool distribution engine
│   └── SimpananPaymentService.php   ← Billing & payment processing
└── Enums/
```

---

## 📌 Core Business Logic

### 1. Savings (Simpanan)
* **Types**: Simpanan Pokok (one-time), Wajib (monthly mandatory), Sukarela (voluntary/withdrawable)
* **Action**: `PaySimpananAction` handles all deposit/withdrawal operations
* **Rules**: `simpanan_pokok` cannot be withdrawn while membership is active

### 2. Loans (Pinjaman)
* **Workflow**: `Pending` → `Approved` (via `ApproveLoanAction`) → `Disbursed` → `In Repayment` (via `ProcessLoanPaymentAction`) → `Completed`
* **Rules**: Late penalties processed during installment payments

### 3. SHU & RAT
* **SHU Calculation**: `ShuCalculationService` computes based on savings contribution + transaction volume
* **Disbursement**: `DisburseShuAction` creates accounting entry + marks distribution

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[01_ARCHITECTURE_GUIDE|Architecture Guide]]
- [[schema_summary|Database Schema]]
- [[mod_akuntansi_audit|Financial Accounting]]
