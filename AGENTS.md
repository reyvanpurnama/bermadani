# Koperasi Bermadani — Workspace Agent Rules & UI/UX Standards

This file defines the project design system, code architecture rules, and UI/UX standards for Koperasi Bermadani UMBandung.

---

## 🎨 UI/UX & Design System Guidelines

### 1. Color Palette & Aesthetics
* **Primary Accent**: `#155A6B` (Deep Teal / Bermadani Primary).
* **Secondary Accents**: Soft Emerald (`#10B981`), Warm Amber/Gold (`#D97706`), Royal Purple (`#7C3AED`).
* **Backgrounds**: Clean off-white (`bg-white`, `bg-zinc-50`, `bg-[#f5f5f7]`).
* **No Harsh Black Outlines**: Strictly avoid `border-zinc-200` or `border-black` outline borders on cards, badges, and image containers. Use soft depth shadows (`shadow-sm hover:shadow-xl`) and subtle background contrasts (`bg-zinc-100/70`).

### 2. Image & Asset Policy
* **No Generated AI Bento Images**: Do not insert low-quality AI-generated image illustrations into feature tiles or step cards.
* **Frame Placeholders**: Use clean, elegant visual frame placeholders with soft icon gradients and subtle labels until real app screenshots are available.
* **Hero Image Preservation**: Keep official landscape and portrait background frames (`images/hero-landscape.jpeg` & `images/hero-portrait.jpeg`) for Hero and Strategic Advantages containers.

### 3. Typography & Spacing
* **Font**: System Apple / Inter / Outfit sans-serif hierarchy.
* **Headlines**: High-contrast, bold tracking-tight titles (`font-extrabold tracking-tight text-zinc-900`).
* **Section Padding**: Generous vertical spacing (`py-16 sm:py-24`) to maintain high-end whitespace.

---

## 🏗️ Code Architecture Rules

### 1. Modular Landing Page Structure
The landing page MUST be kept modular using Blade partials located in `resources/views/landing/partials/`:
* `navbar.blade.php` — Global header navigation.
* `hero.blade.php` — Hero showcase & main CTAs.
* `features.blade.php` — Core member facilities & interactive product tabs.
* `supplier.blade.php` — Supplier & UMKM 3-step consignment workflow.
* `advantages.blade.php` — Strategic advantages & metrics showcase.
* `faq.blade.php` — Accordion FAQ section.
* `footer.blade.php` — Footer links & legal notices.

### 2. Tailwind CSS v4 & Alpine.js Best Practices
* **Flexbox/Grid Squeezing**: Always wrap Alpine.js split layouts with explicit flexbox/grid widths (`w-full lg:w-5/12` and `w-full lg:w-7/12 min-w-0`) to prevent Tailwind CSS v4 column squeezing bug.
* **Flicker Prevention**: Always add `x-cloak` and `style="display: none;"` to hidden Alpine.js tab elements.
