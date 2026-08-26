# 00_OVERVIEW: Koperasi Bermadani System

Welcome to the **Koperasi Bermadani (web-koperasi-umb)** knowledge base. This documentation vault is designed using atomic markdown notes to maximize readability and AI token efficiency.

---

## 📌 Project Summary

* **Project Name**: Web Koperasi Bermadani (Koperasi UMB)
* **Framework**: Laravel 10 (PHP 8.1+)
* **Frontend**: Blade Templates, TailwindCSS, Livewire, Vite
* **Database**: MySQL / MariaDB
* **Architecture**: **Modular Monolith** (Domain-Driven) ✅ Refactored
* **Core Domains**: 
  1. [[mod_koperasi|Koperasi Core]] — `app/Domains/Koperasi/` (Member, Simpanan, Pinjaman, SHU, RAT)
  2. [[mod_minimarket_pos|Minimarket & POS]] — `app/Domains/Minimarket/` (Kasir, Transaksi, Stok, Shift, Poin)
  3. [[mod_supplier|Supplier & Konsinyasi]] — `app/Domains/Supplier/` (Supplier, Konsinyasi, Payout, Restock)
  4. [[mod_akuntansi_audit|Akuntansi & Audit]] — `app/Domains/Accounting/` (Laporan Keuangan, Bank Reconcile)
  5. **Shared** — `app/Shared/` (User, ActivityLog, UserRole)

---

## 🏗️ Architecture Overview

```text
app/
├── Domains/
│   ├── Koperasi/       (Models, Actions, Services, Enums)
│   ├── Minimarket/     (Models, Actions, Services, Enums)
│   ├── Supplier/       (Models, Actions, Services, Enums)
│   └── Accounting/     (Models, Actions, Services)
├── Shared/             (User, ActivityLog, UserRole — cross-domain)
├── Http/Controllers/   (Slim controllers — delegates to Services/Actions)
├── Livewire/           (72 components — UI layer)
└── Console/Commands/   (16 CLI commands)
```

> **Backward Compatibility**: Alias classes at `app/Models/` and `app/Services/` ensure all existing `use App\Models\X` imports still work.

---

## 📂 Vault Structure

- **Architecture**: [[01_ARCHITECTURE_GUIDE|Modular Monolith & Action Pattern]]
- **Standards**: [[Coding_Standards|Token-Efficient AI Prompting & Coding Guidelines]]
- **Database Schema**: [[schema_summary|All 36 Models & Tables]] | [[ERD_relationships|Entity Relationships]]
- **Domain Modules**:
  - [[mod_koperasi|Simpanan, Pinjaman, & SHU]]
  - [[mod_minimarket_pos|POS Kasir, Inventory, & Shift]]
  - [[mod_supplier|Supplier, Konsinyasi, & Payout]]
  - [[mod_akuntansi_audit|Jurnal Keuangan & Reconcile Bank]]
- **Refactoring**: [[Refactoring_Log|Domain Refactoring Changelog]]

---

## 🚀 Quick Commands

```bash
# Local development server
php artisan serve

# Frontend asset compilation
npm run dev

# Run database migrations / seeders
php artisan migrate --seed

# Rebuild autoloader after domain changes
composer dump-autoload
```
