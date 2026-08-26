# Coding Standards & AI Prompting Guidelines

Guidelines for keeping code clean, maintainable, and highly token-efficient when pairing with AI assistants.

---

## ⚡ Token Efficiency Rules

1. **Attach Atomic Docs, Not Raw Folders**:
   When asking AI about a feature (e.g. POS Cashier), attach `docs/Modules/mod_minimarket_pos.md` and `docs/Database/schema_summary.md` instead of attaching 15 PHP controller/view files.

2. **Keep Classes Under 150 Lines**:
   Break large controllers or models into single **Action Classes** or **Services**. Smaller files consume fewer tokens per prompt iteration and reduce AI context hallucination.

3. **Use Markdown/XML Tags in Prompts**:
   Structure prompt inputs with explicit markdown headers or tags:
   ```markdown
   <context>
   Refer to docs/Modules/mod_koperasi.md
   </context>
   <instruction>
   Add validation for maximum monthly loan withdrawal.
   </instruction>
   ```

---

## 🛠️ Laravel Development Standards

* **Strict Types**: Always use `declare(strict_types=1);` where applicable.
* **Database Transactions**: Any multi-model mutation must be wrapped in `DB::transaction()`.
* **Form Requests**: Controllers must delegate input validation to `FormRequest` classes.
* **Enums for Statuses**: Use PHP 8.1 Enums (located in `app/Enums/`) for statuses (e.g. LoanStatus, SupplierStatus).

---

## 🔗 Related Notes
- [[00_OVERVIEW|Overview]]
- [[01_ARCHITECTURE_GUIDE|Architecture Guide]]
